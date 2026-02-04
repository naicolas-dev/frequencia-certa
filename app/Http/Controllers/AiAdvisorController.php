<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Disciplina;
use App\Services\DisciplinaStatsService;
use App\Helpers\AiCacheHelper;
use App\Helpers\AiCredits;
use App\Models\AiChatMessage;
use Carbon\Carbon;


class AiAdvisorController extends Controller
{
    /**
     * 📏 Helper: Calculate TTL until end of day for cache.
     * 
     * @param string $date Y-m-d format
     * @return int seconds until end of day
     */
    private function cacheTtlUntilEndOfDay(string $date): int
    {
        $targetDate = Carbon::parse($date, config('app.timezone'));
        $endOfDay = $targetDate->copy()->endOfDay();
        $now = Carbon::now(config('app.timezone'));

        // If the date is in the past, cache for 24 hours (already immutable)
        if ($targetDate->isPast() && !$targetDate->isToday()) {
            return 86400; // 24 hours
        }

        // For today or future, cache until end of that day
        $diff = $now->diffInSeconds($endOfDay, false);

        // Ensure minimum 60 seconds TTL
        return max(60, $diff);
    }

    /**
     * 🤖 NEW ENDPOINT: Daily AI Advice (Cached)
     * 
     * GET /api/ai-advisor/day-check?date=YYYY-MM-DD
     * 
     * Returns strategic advice for whether to skip classes on a given day.
     * Response is cached until end-of-day to avoid repeated LLM calls.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dayCheck(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date|after_or_equal:' . now()->startOfYear()->format('Y-m-d')
        ]);

        $user = Auth::user();
        $date = $request->query('date', now()->format('Y-m-d'));
        $dateCarbon = Carbon::parse($date);
        $dayOfWeekIso = $dateCarbon->dayOfWeekIso;

        // 📦 1. CHECK CACHE FIRST
        $cacheKey = AiCacheHelper::dayCheckKey($user->id, $date);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $cached['cached'] = true;
            $cached['cost_applied'] = 0;
            $cached['user_credits'] = $user->ai_credits;
            return response()->json($cached);
        }

        // 💰 2. CREDIT CHECK (Cache miss - will call LLM)
        $user->ensureMonthlyCreditsFresh();

        if (!$user->hasEnoughCredits(AiCredits::COST_DAY_CHECK)) {
            return response()->json([
                'message' => 'Your wisdom credits are exhausted for this month.',
                'error' => 'insufficient_credits',
                'user_credits' => $user->ai_credits,
                'monthly_max' => $user->getMonthlyMaxCredits(),
                'reset_at' => $user->credits_reset_at?->toIso8601String(),
            ], 402);
        }

        // 🐎 2. BUILD DAY CONTEXT (No N+1 Queries)
        // Fetch all disciplines that have classes on this day of week
        $disciplinas = Disciplina::where('user_id', $user->id)
            ->whereHas('horarios', function ($q) use ($dayOfWeekIso) {
                $q->where('dia_semana', $dayOfWeekIso);
            })
            ->where(function ($q) use ($date) {
                // Only include if within semester dates
                $q->whereNull('data_inicio')
                    ->orWhere('data_inicio', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('data_fim')
                    ->orWhere('data_fim', '>=', $date);
            })
            ->with(['horarios']) // ✅ FIX: Load ALL horarios (not filtered by day) for StatsService
            ->withCount([
                'frequencias as total_aulas_realizadas',
                'frequencias as total_faltas' => function ($q) {
                    $q->where('presente', false);
                }
            ])
            ->orderBy('nome')
            ->get();

        // If no classes today, short-circuit (No LLM call = No charge)
        if ($disciplinas->isEmpty()) {
            $response = [
                'message' => 'Nenhuma aula agendada para este dia. Aproveite o descanso! 🎉',
                'risk' => 'NONE',
                'date' => $date,
                'subjects' => [],
                'cached' => false,
                'cost_applied' => 0,
                'user_credits' => $user->ai_credits,
            ];

            // Cache this "no classes" response
            $ttl = $this->cacheTtlUntilEndOfDay($date);
            Cache::put($cacheKey, $response, $ttl);

            $response['cached'] = false;
            return response()->json($response);
        }

        // 🛠️ 3. ENRICH WITH STATS (Batch computation)
        $statsService = app(DisciplinaStatsService::class);
        $statsService->enrichWithStats($disciplinas, $user);

        // 📊 4. BUILD CONTEXT PAYLOAD FOR LLM
        $dayContext = [];
        $overallRisk = 'LOW';
        $criticalCount = 0;
        $warningCount = 0;

        foreach ($disciplinas as $disciplina) {
            $total_aulas_realizadas = $disciplina->total_aulas_realizadas ?? 0;
            $total_faltas = $disciplina->total_faltas ?? 0;
            $total_previsto = $disciplina->getAttribute('total_aulas_previstas_cache') ?? 0;

            // ✅ P0-2 FIX: Skip disciplines with no projected classes (avoid false CRITICAL)
            if ($total_previsto === 0) {
                \Log::info("Discipline {$disciplina->nome} skipped - no projected classes");
                continue; // Skip disciplines without date range or schedule
            }

            $limite_faltas = floor($total_previsto * 0.25);
            $restantes = max(0, $limite_faltas - $total_faltas);

            // Compute attendance rate
            if ($total_aulas_realizadas > 0) {
                $presencas = $total_aulas_realizadas - $total_faltas;
                $taxa_presenca = round(($presencas / $total_aulas_realizadas) * 100);
            } else {
                $taxa_presenca = 0;
            }

            // Determine status
            $status = 'OK';
            if ($restantes <= 0) {
                $status = 'CRITICAL';
                $criticalCount++;
            } elseif ($restantes <= 2) {
                $status = 'WARNING';
                $warningCount++;
            }

            // Get first class time for TODAY only (filter after stats computation)
            $todaysHorarios = $disciplina->horarios->where('dia_semana', $dayOfWeekIso);
            $firstClass = $todaysHorarios->sortBy('horario_inicio')->first();
            $horario = $firstClass ? substr($firstClass->horario_inicio, 0, 5) : 'N/A';

            $dayContext[] = [
                'nome' => $disciplina->nome,
                'horario' => $horario,
                'presenca_atual' => $taxa_presenca . '%',
                'faltas_usadas' => $total_faltas,
                'faltas_restantes' => $restantes,
                'limite_total' => $limite_faltas,
                'status' => $status
            ];
        }

        // Determine overall risk
        if ($criticalCount > 0) {
            $overallRisk = 'HIGH';
        } elseif ($warningCount > 0) {
            $overallRisk = 'MEDIUM';
        }

        // 🤖 5. CALL LLM (Gemini)
        try {
            $aiResponse = $this->callGeminiForDayAdvice($dayContext, $dateCarbon, $overallRisk);

            // ✅ SUCCESS: Deduct credits and build response
            $user->deductCredits(AiCredits::COST_DAY_CHECK);

            $response = [
                'message' => $aiResponse['message'] ?? 'Consulte seus dados e decida com sabedoria.',
                'risk' => $aiResponse['risk'] ?? $overallRisk,
                'date' => $date,
                'subjects' => $dayContext,
                'cached' => false,
                'cost_applied' => AiCredits::COST_DAY_CHECK,
                'user_credits' => $user->ai_credits, // After deduction
            ];

            // 📦 Cache the successful response
            $ttl = $this->cacheTtlUntilEndOfDay($date);
            Cache::put($cacheKey, $response, $ttl);

            // 💾 SAVE HISTORY
            // User Message
            AiChatMessage::create([
                'user_id' => $user->id,
                'role' => 'user',
                'content' => "Analise o meu dia {$dateCarbon->format('d/m')}. Posso faltar?",
                'meta' => ['type' => 'day_check', 'date' => $date]
            ]);

            // AI Message
            AiChatMessage::create([
                'user_id' => $user->id,
                'role' => 'ai',
                'content' => $response['message'],
                'meta' => [
                    'risk' => $response['risk'],
                    'subjects' => $dayContext,
                    'cost' => AiCredits::COST_DAY_CHECK
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Day Check failed', ['error' => $e->getMessage()]);

            // ❌ FAILURE: Do NOT deduct credits, do NOT cache
            $response = [
                'message' => 'O oráculo está temporariamente indisponível. Baseie-se nos dados: ' .
                    ($criticalCount > 0 ? 'Você tem matérias CRÍTICAS hoje. Recomendo ir!' : 'Avalie os números com cuidado.'),
                'risk' => $overallRisk,
                'date' => $date,
                'subjects' => $dayContext,
                'cached' => false,
                'cost_applied' => 0,
                'user_credits' => $user->ai_credits, // No deduction
            ];
        }

        $response['cached'] = false;
        return response()->json($response);
    }

    /**
     * Call Gemini API with day context.
     * 
     * @param array $dayContext
     * @param Carbon $date
     * @param string $initialRisk
     * @return array ['message' => string, 'risk' => string]
     * @throws \Exception
     */
    private function callGeminiForDayAdvice(array $dayContext, Carbon $date, string $initialRisk): array
    {
        $apiKey = config('gemini.key');
        $modelName = config('gemini.model');
        $base = config('gemini.url');

        if (empty($apiKey) || empty($modelName) || empty($base)) {
            throw new \Exception('Gemini API not configured');
        }

        $url = "{$base}{$modelName}:generateContent?key={$apiKey}";

        // Build context string
        $contextStr = '';
        foreach ($dayContext as $subject) {
            $contextStr .= "- {$subject['nome']} ({$subject['horario']}): ";
            $contextStr .= "{$subject['presenca_atual']} presença, ";
            $contextStr .= "{$subject['faltas_usadas']}/{$subject['limite_total']} faltas, ";
            $contextStr .= "restam {$subject['faltas_restantes']}, status: {$subject['status']}\n";
        }

        $dayName = $date->locale('pt_BR')->dayName;
        $dateFormatted = $date->format('d/m/Y');

        $prompt = "
Você é um conselheiro acadêmico estratégico experiente.

CONTEXTO:
O aluno tem as seguintes aulas hoje ({$dayName}, {$dateFormatted}):

{$contextStr}

TAREFA:
Avalie se o aluno pode faltar hoje considerando:
1. Matérias com status CRITICAL (faltas restantes <= 0) - ALTO RISCO
2. Matérias com status WARNING (faltas restantes <= 2) - RISCO MÉDIO
3. Momento do semestre (início/meio/fim)
4. Distribuição de risco entre as matérias

RETORNE APENAS JSON:
{
  \"message\": \"frase curta e direta (máx 150 caracteres) com recomendação\",
  \"risk\": \"LOW|MEDIUM|HIGH\",
  \"key_reasons\": [\"motivo1\", \"motivo2\"]
}
";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(20)->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                        'temperature' => 0.8
                    ]
                ]);

        if ($response->failed()) {
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }

        $jsonResponse = $response->json();
        $textoGerado = data_get($jsonResponse, 'candidates.0.content.parts.0.text');

        if (empty($textoGerado)) {
            throw new \Exception('Empty response from Gemini');
        }

        $dados = json_decode($textoGerado, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($dados['message'], $dados['risk'])) {
            throw new \Exception('Invalid JSON from Gemini: ' . $textoGerado);
        }

        return $dados;
    }

    public function analisarRisco(Disciplina $disciplina)
    {
        // [SEGURANÇA] 1. Protege o acesso à disciplina
        if (Auth::id() !== $disciplina->user_id) {
            Log::warning("Tentativa de acesso não autorizado: User " . Auth::id() . " tentou acessar disciplina " . $disciplina->id);
            abort(403, 'Você não tem permissão para acessar esta disciplina.');
        }

        $user = Auth::user();
        $date = now()->format('Y-m-d');

        // 📦 CHECK CACHE FIRST
        $cacheKey = AiCacheHelper::subjectAnalysisKey($user->id, $disciplina->id, $date);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $cached['cached'] = true;
            $cached['cost_applied'] = 0;
            $cached['user_credits'] = $user->ai_credits;
            return response()->json($cached);
        }

        // 💰 CREDIT CHECK (Cache miss - will call LLM)
        $user->ensureMonthlyCreditsFresh();

        if (!$user->hasEnoughCredits(AiCredits::COST_SUBJECT_ANALYSIS)) {
            return response()->json([
                'message' => 'Your wisdom credits are exhausted for this month.',
                'error' => 'insufficient_credits',
                'user_credits' => $user->ai_credits,
                'monthly_max' => $user->getMonthlyMaxCredits(),
                'reset_at' => $user->credits_reset_at?->toIso8601String(),
            ], 402);
        }

        // --- PREPARAÇÃO DOS DADOS ---

        // Carrega relacionamentos
        $disciplina->load('frequencias');

        $totalAulasRealizadas = $disciplina->frequencias->count();
        $faltas = $disciplina->frequencias->where('presente', false)->count();

        // 2. Tratar divisão por zero ou null em total_aulas_previstas
        $totalPrevistas = $disciplina->total_aulas_previstas;
        if (!$totalPrevistas || $totalPrevistas <= 0) {
            $limiteFaltas = 0;
        } else {
            $limiteFaltas = floor($totalPrevistas * 0.25);
        }

        // 3. Normalizar restantes (não deixar negativo)
        $restantes = max(0, $limiteFaltas - $faltas);

        // 4. Cálculo de presença seguro
        $presenca = 100;
        if ($totalAulasRealizadas > 0) {
            $presenca = round((($totalAulasRealizadas - $faltas) / $totalAulasRealizadas) * 100);
        }

        // [NOVO] 5. Contexto Temporal (Para evitar redundância)
        $hoje = now()->format('d/m/Y');
        $mes = now()->month;

        // Define a fase do ano para a IA entender a gravidade
        $contextoTemporal = match (true) {
            $mes <= 3 => "INÍCIO do ano letivo. (Faltar agora é perigoso pois queima margem cedo)",
            $mes <= 6 => "MEIO do 1º semestre.",
            $mes == 7 => "FÉRIAS de meio de ano chegando.",
            $mes <= 9 => "INÍCIO do 2º semestre.",
            $mes <= 11 => "RETA FINAL. (Se tiver sobrando faltas, é tranquilo)",
            default => "Últimos dias de aula."
        };

        // --- CHAMADA À API ---

        // 6. Validação da API Key
        $apiKey = config('gemini.key');
        if (empty($apiKey)) {
            Log::error('GEMINI_API_KEY não configurada no .env');
            return $this->fallbackResponse('Erro de configuração no servidor (API Key).');
        }

        $modelName = config('gemini.model');
        if (empty($modelName)) {
            Log::error('GEMINI_MODEL não configurada no .env');
            return $this->fallbackResponse('Erro de configuração no servidor (Model Name).');
        }

        $base = config('gemini.url');
        if (empty($base)) {
            Log::error('GEMINI_URL não configurada no .env');
            return $this->fallbackResponse('Erro de configuração no servidor (Base URL).');
        }

        // URL da API
        $url = "{$base}{$modelName}:generateContent?key={$apiKey}";

        // [NOVO] 7. Prompt com Contexto de Tempo
        $prompt = "
            Atue como um conselheiro acadêmico estratégico e direto. O aluno quer faltar na matéria '{$disciplina->nome}'.

            DADOS TÉCNICOS:
            - Data de hoje: {$hoje}
            - Fase do Ano: {$contextoTemporal} (Ex: Início do semestre, Reta final, Meio do ano)
            - Presença atual: {$presenca}%
            - Faltas já usadas: {$faltas}
            - Faltas Restantes (Saldo): {$restantes}
            - Limite total: {$limiteFaltas}

            TAREFA:
            Analise o risco com base no saldo de faltas e, principalmente, no momento do ano. Use um tom de veterano: amigável, prático e sem gírias forçadas.

            LÓGICA DE ANÁLISE:
            1. RISCO ALTO: Se ele já gastou muitas faltas e ainda estamos no INÍCIO ou MEIO do período. Dê um aviso real de que o semestre é longo e ele vai ficar sem margem rápido demais.
            2. RISCO MÉDIO: Se o saldo está condizente com o tempo que falta, mas exige cautela para não acumular.
            3. RISCO BAIXO: Se ele tem muitas faltas sobrando e já estamos na RETA FINAL. Pode ser mais flexível e dizer que ele conquistou esse descanso.
            4. PONTO CRÍTICO: Se {$restantes} for menor que 3, o risco é ALTO independente da data.

            Retorne APENAS JSON: { \"analise\": \"frase curta e direta\", \"risco\": \"BAIXO/MEDIO/ALTO\", \"emoji\": \"icone\" }
        ";

        try {
            // 8. Requisição Segura (Sem withoutVerifying para produção)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(20)->post($url, [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'response_mime_type' => 'application/json',
                            'temperature' => 1.0
                        ]
                    ]);

            if ($response->failed()) {
                Log::error('Erro API Google: ' . $response->body());
                return $this->fallbackResponse('O oráculo está confuso agora (Erro API).');
            }

            $jsonResponse = $response->json();

            // 9. Leitura segura com data_get
            $textoGerado = data_get($jsonResponse, 'candidates.0.content.parts.0.text');

            if (empty($textoGerado)) {
                Log::error('Resposta vazia ou inválida do Google: ' . json_encode($jsonResponse));
                return $this->fallbackResponse('O oráculo ficou mudo.');
            }

            // 10. Validação do JSON
            $dados = json_decode($textoGerado, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($dados['analise'], $dados['risco'])) {
                Log::error('JSON malformado retornado pela IA: ' . $textoGerado);
                return $this->fallbackResponse('Erro na tradução da profecia.');
            }

            // ✅ SUCCESS: Deduct credits and cache response
            $user->deductCredits(AiCredits::COST_SUBJECT_ANALYSIS);

            // Add credit info to response
            $dados['cost_applied'] = AiCredits::COST_SUBJECT_ANALYSIS;
            $dados['user_credits'] = $user->ai_credits;
            $dados['cached'] = false;

            // Cache for end of day
            $ttl = $this->cacheTtlUntilEndOfDay($date);
            Cache::put($cacheKey, $dados, $ttl);

            // 💾 SAVE HISTORY
            // User Message
            AiChatMessage::create([
                'user_id' => $user->id,
                'role' => 'user',
                'content' => "Analise minha situação em {$disciplina->nome}. Posso faltar?",
                'meta' => ['type' => 'subject_analysis', 'subject_id' => $disciplina->id]
            ]);

            // AI Message
            AiChatMessage::create([
                'user_id' => $user->id,
                'role' => 'ai',
                'content' => $dados['analise'],
                'meta' => [
                    'risk' => $dados['risco'],
                    'emoji' => $dados['emoji'],
                    'cost' => AiCredits::COST_SUBJECT_ANALYSIS
                ]
            ]);

            return response()->json($dados);

        } catch (\Exception $e) {
            Log::error('Exceção no AiAdvisor: ' . $e->getMessage());
            return $this->fallbackResponse('Erro técnico ao consultar a IA.');
        }
    }

    /**
     * Fallback para erros (NO credit charge)
     */
    private function fallbackResponse($msg)
    {
        $user = Auth::user();
        return response()->json([
            'analise' => $msg . ' Mas na dúvida: VÁ PARA A AULA!',
            'risco' => 'ALTO',
            'emoji' => '🤖',
            'cost_applied' => 0,
            'user_credits' => $user->ai_credits,
            'cached' => false,
        ]);
    }

    /**
     * GET /api/ai-advisor/history
     * Returns the chat history for the authenticated user.
     */
    public function history(Request $request)
    {
        $messages = AiChatMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc') // Oldest first for chat UI
            ->limit(50) // Limit to last 50 interactions
            ->get();

        return response()->json($messages);
    }
}
