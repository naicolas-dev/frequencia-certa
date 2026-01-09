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
    public function index()
    {
        return view('grade.importar');
    }

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

            $promptInstruction = "
                $contexto
                
                TAREFA:
                Converta essa grade escolar em um JSON Array estrito.
                
                REGRAS:
                1. Identifique o Dia.
                2. Extraia a ordem (número da aula) e o nome da disciplina limpo.
                3. Tente identificar uma cor para a matéria (hexadecimal) se houver dica visual ou no texto. Caso contrário, retorne null.
                4. NÃO inclua explicações. Apenas JSON.

                MODELO DE SAÍDA (Array de Objetos):
                [
                    {
                        \"nome_dia\": \"Segunda-Feira\",
                        \"aulas\": [
                            { \"ordem\": 1, \"disciplina\": \"Matemática\", \"cor\": \"#FF0000\" },
                            { \"ordem\": 2, \"disciplina\": \"História\", \"cor\": null }
                        ]
                    }
                ]
            ";

            $parts[] = ['text' => $promptInstruction];

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
            
            if (preg_match('/\[.*\]/s', $jsonRaw, $matches)) {
                $jsonRaw = $matches[0];
            }

            $dados = json_decode($jsonRaw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Erro no formato da resposta da IA.'], 422);
            }

            // Mapa para consistência de cores
            $coresDefinidas = [];

            foreach ($dados as &$dia) {
                if (isset($dia['aulas']) && is_array($dia['aulas'])) {
                    foreach ($dia['aulas'] as &$aula) {
                        
                        // 1. Evitar "Matéria Indefinida" genérica
                        $rawDisciplina = $aula['disciplina'] ?? '';
                        
                        if (!is_string($rawDisciplina) || trim($rawDisciplina) === '') {
                            $diaNome = $dia['nome_dia'] ?? 'Dia';
                            $ordemAula = $aula['ordem'] ?? '?';
                            $rawDisciplina = "Matéria ($diaNome - $ordemAula)";
                            $aula['disciplina'] = $rawDisciplina;
                        }

                        $nomeKey = mb_strtoupper(trim($rawDisciplina));

                        // 2. Lógica de Cores (Consistente)
                        if (!empty($aula['cor']) && preg_match('/^#[a-f0-9]{6}$/i', $aula['cor'])) {
                            $coresDefinidas[$nomeKey] = $aula['cor'];
                        } 
                        elseif (isset($coresDefinidas[$nomeKey])) {
                            $aula['cor'] = $coresDefinidas[$nomeKey];
                        } 
                        else {
                            $novaCor = $this->gerarCorAleatoria();
                            $coresDefinidas[$nomeKey] = $novaCor;
                            $aula['cor'] = $novaCor;
                        }
                    }
                }
            }
            unset($dia);
            unset($aula);

            return response()->json(['data' => $dados]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Erro técnico no servidor.'], 500);
        }
    }

    public function salvarLote(Request $request)
    {
        $dados = $request->input('dados');
        $config = $request->input('configuracao');

        if (!$dados || !is_array($dados)) {
            return response()->json(['error' => 'Dados inválidos.'], 400);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $dataInicio = $user->ano_letivo_inicio ?? now(); 
            $dataFim    = $user->ano_letivo_fim    ?? now()->addMonths(6);

            foreach ($dados as $dia) {
                $diaSemanaInt = $this->converterDiaParaInt($dia['nome_dia']);
                if ($diaSemanaInt === null) continue;

                foreach ($dia['aulas'] as $aula) {
                    
                    $nomeRaw = $aula['disciplina'] ?? 'Matéria Desconhecida';
                    $nomeDisciplina = mb_convert_case($nomeRaw, MB_CASE_TITLE, "UTF-8");
                    
                    $disciplina = Disciplina::firstOrNew([
                        'user_id' => Auth::id(),
                        'nome' => $nomeDisciplina
                    ]);

                    $disciplina->data_inicio = $dataInicio;
                    $disciplina->data_fim = $dataFim;

                    $disciplina->carga_horaria_total = 80; 

                    // Lógica de blindagem da cor
                    $corSegura = !empty($aula['cor']) ? $aula['cor'] : $this->gerarCorAleatoria();

                    if (!$disciplina->exists || empty($disciplina->cor)) {
                        $disciplina->cor = $corSegura;
                    }

                    $disciplina->save();

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

    private function calcularHorarioDinamico($ordem, $config) {
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