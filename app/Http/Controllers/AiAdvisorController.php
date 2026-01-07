<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;

class AiAdvisorController extends Controller
{
    public function analisarRisco(Disciplina $disciplina)
    {
        // [SEGURANÇA] 1. Protege o acesso à disciplina
        if (Auth::id() !== $disciplina->user_id) {
            Log::warning("Tentativa de acesso não autorizado: User " . Auth::id() . " tentou acessar disciplina " . $disciplina->id);
            abort(403, 'Você não tem permissão para acessar esta disciplina.');
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
        $contextoTemporal = match(true) {
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

        $modelName = 'gemini-2.5-flash';

        // URL da API (Usando a versão flash sugerida)
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

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
            ])->timeout(10)->post($url, [
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

            return response()->json($dados);

        } catch (\Exception $e) {
            Log::error('Exceção no AiAdvisor: ' . $e->getMessage());
            return $this->fallbackResponse('Erro técnico ao consultar a IA.');
        }
    }

    /**
     * Fallback para erros
     */
    private function fallbackResponse($msg)
    {
        return response()->json([
            'analise' => $msg . ' Mas na dúvida: VÁ PARA A AULA!',
            'risco' => 'ALTO',
            'emoji' => '🤖'
        ]);
    }
}
