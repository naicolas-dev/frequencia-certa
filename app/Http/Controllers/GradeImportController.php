<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;
use App\Models\GradeHoraria;

class GradeImportController extends Controller
{
    // Exibe a tela
    public function index()
    {
        return view('grade.importar');
    }

    // Processa a IA (Lê a grade)
    public function processar(Request $request)
    {
        $request->validate([
            'texto_grade' => 'nullable|string|required_without:foto_grade',
            'foto_grade'  => 'nullable|image|max:5120|required_without:texto_grade',
        ]);

        try {
            $apiKey = config('gemini.key');
            $modelName = config('gemini.model'); 
            $base = config('gemini.url');
            $url = "{$base}{$modelName}:generateContent?key={$apiKey}";

            $parts = [];

            if ($request->hasFile('foto_grade')) {
                // Lógica de imagem
                $image = $request->file('foto_grade');
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $image->getMimeType(),
                        'data' => base64_encode(file_get_contents($image->getRealPath()))
                    ]
                ];
                $contexto = "Analise esta imagem da grade horária.";
            } else {
                $textoUsuario = $request->input('texto_grade');
                $contexto = "Analise este texto cru fornecido pelo aluno:\n\n---\n$textoUsuario\n---";
            }

            // PROMPT
            $promptInstruction = "
                $contexto
                
                TAREFA:
                Converta essa grade escolar em um JSON Array estrito.
                
                PADRÃO DE ENTRADA:
                O aluno mandou algo como '1º-Matéria' ou '1 - Matéria'. 
                
                REGRAS:
                1. Identifique o Dia.
                2. Extraia a ordem (número da aula) e o nome da disciplina limpo.
                3. NÃO inclua explicações. NÃO use Markdown. Apenas o JSON cru.

                MODELO DE SAÍDA (Array de Objetos):
                [
                    {
                        \"nome_dia\": \"Segunda-Feira\",
                        \"aulas\": [
                            { \"ordem\": 1, \"disciplina\": \"Matemática\" },
                            { \"ordem\": 2, \"disciplina\": \"História\" }
                        ]
                    }
                ]
            ";

            $parts[] = ['text' => $promptInstruction];

            // Chamada API
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                        'temperature' => 0.1
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Erro Gemini Import: ' . $response->body());
                return response()->json(['error' => 'Erro ao conectar com a IA.'], 500);
            }

            $jsonRaw = data_get($response->json(), 'candidates.0.content.parts.0.text');
            
            // Log e Limpeza
            Log::info("RESPOSTA BRUTA IA: " . $jsonRaw);

            if (preg_match('/\[.*\]/s', $jsonRaw, $matches)) {
                $jsonRaw = $matches[0];
            }

            $dados = json_decode($jsonRaw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON ERROR: ' . json_last_error_msg());
                return response()->json(['error' => 'A IA respondeu, mas o formato veio quebrado. Tente de novo.'], 422);
            }

            return response()->json(['data' => $dados]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Erro técnico no servidor.'], 500);
        }
    }

    // Salva no Banco (Lote)
    public function salvarLote(Request $request)
    {
        $dados = $request->input('dados');
        $config = $request->input('configuracao'); // Recebe do front

        if (!$dados || !is_array($dados)) {
            return response()->json(['error' => 'Dados inválidos.'], 400);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();
            // Usa os campos corretos do Model User.php
            $dataInicio = $user->ano_letivo_inicio ?? now(); 
            $dataFim    = $user->ano_letivo_fim    ?? now()->addMonths(6);

            foreach ($dados as $dia) {
                $diaSemanaInt = $this->converterDiaParaInt($dia['nome_dia']);
                if ($diaSemanaInt === null) continue;

                foreach ($dia['aulas'] as $aula) {
                    $nomeDisciplina = mb_convert_case($aula['disciplina'], MB_CASE_TITLE, "UTF-8");
                    
                    // Cria ou recupera a disciplina
                    $disciplina = Disciplina::firstOrCreate(
                        ['user_id' => Auth::id(), 'nome' => $nomeDisciplina],
                        [
                            'data_inicio' => $dataInicio, 
                            'data_fim' => $dataFim,    
                            'total_aulas_previstas' => 80, 
                            'cor_hex' => $this->gerarCorAleatoria() 
                        ]
                    );

                    // CÁLCULO DINÂMICO DE HORÁRIO
                    $horarios = $this->calcularHorarioDinamico($aula['ordem'], $config);

                    GradeHoraria::create([
                        'user_id'        => Auth::id(),
                        'disciplina_id'  => $disciplina->id,
                        'dia_semana'     => $diaSemanaInt,
                        'horario_inicio' => $horarios['inicio'],
                        'horario_fim'    => $horarios['fim'],
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Grade criada com sucesso!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao salvar grade: " . $e->getMessage());
            return response()->json(['error' => 'Erro técnico: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calcula o horário baseado na ordem da aula e config do usuário.
     */
    private function calcularHorarioDinamico($ordem, $config)
    {
        $horaBaseStr    = $config['inicio'] ?? '07:00';
        $duracaoAula    = intval($config['duracao'] ?? 50);
        $duracaoRecreio = intval($config['intervaloTempo'] ?? 15);
        $aposAulaNum    = intval($config['intervaloApos'] ?? 3);

        $horaAtual = Carbon::createFromFormat('H:i', $horaBaseStr);

        for ($i = 1; $i < $ordem; $i++) {
            $horaAtual->addMinutes($duracaoAula);
            
            if ($i == $aposAulaNum) {
                $horaAtual->addMinutes($duracaoRecreio);
            }
        }

        $inicio = $horaAtual->format('H:i');
        $horaAtual->addMinutes($duracaoAula);
        $fim = $horaAtual->format('H:i');

        return ['inicio' => $inicio, 'fim' => $fim];
    }

    /**
     * Converte string de dia para inteiro (Segunda = 1)
     */
    private function converterDiaParaInt($texto) {
        $texto = mb_strtolower($texto);
        if (str_contains($texto, 'segunda')) return 1;
        if (str_contains($texto, 'terça') || str_contains($texto, 'terca')) return 2;
        if (str_contains($texto, 'quarta')) return 3;
        if (str_contains($texto, 'quinta')) return 4;
        if (str_contains($texto, 'sexta')) return 5;
        if (str_contains($texto, 'sábado') || str_contains($texto, 'sabado')) return 6;
        if (str_contains($texto, 'domingo')) return 0;
        return null;
    }

    private function gerarCorAleatoria() {
        $cores = ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#d946ef', '#f43f5e'];
        return $cores[array_rand($cores)];
    }
}