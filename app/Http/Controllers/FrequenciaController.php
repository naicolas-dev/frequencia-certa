<?php

namespace App\Http\Controllers;

use App\Models\GradeHoraria;
use App\Models\Frequencia;
use App\Services\CalendarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FrequenciaController extends Controller
{
    // API: Busca aulas de uma data específica (com histórico)
    public function buscarPorData(Request $request)
    {
        // Pega a data da URL (?data=2025-12-18) ou usa Hoje se não vier nada
        $dataAlvo = $request->query('data', now()->format('Y-m-d'));

        // 🚫 BLOQUEIO: verifica se o dia é livre
        $diaLivre = app(CalendarioService::class)
            ->verificarDiaLivre($dataAlvo);

        if ($diaLivre) {
            return response()->json([
                'dia_livre' => true,
                'motivo' => $diaLivre['titulo'],
            ]);
        }
        
        // Descobre o dia da semana dessa data (1=Segunda ... 7=Domingo)
        $diaSemana = Carbon::parse($dataAlvo)->dayOfWeekIso;

        // 1. Busca a Grade Horária daquele dia da semana
        $grade = GradeHoraria::where('dia_semana', $diaSemana)
            ->where('user_id', Auth::id())
            ->with('disciplina')
            ->get()
            ->unique('disciplina_id');

        // 2. Busca o Histórico: O que já foi gravado nessa data?
        $historico = Frequencia::where('user_id', Auth::id())
            ->whereDate('data', $dataAlvo)
            ->get()
            ->keyBy('disciplina_id'); // Facilita a busca

        // 3. Mescla os dois: Grade + O que foi marcado
        $resultado = $grade->map(function($aula) use ($historico) {
            $registro = $historico->get($aula->disciplina_id);

            return [
                'disciplina_id' => $aula->disciplina->id,
                'nome' => $aula->disciplina->nome,
                'cor' => $aula->disciplina->cor,
                'horario' => $aula->horario_inicio,
                // Se existir registro, usa ele. Se não, padrão é true (Presente)
                'presente' => $registro ? (bool)$registro->presente : true,
                'ja_registrado' => $registro ? true : false // Para saber se é edição ou novo
            ];
        })->values();

        return response()->json($resultado);
    }

    // API: Salva (Atualiza ou Cria)
    public function registrarLote(Request $request)
    {
        $dados = $request->validate([
            'data' => 'required|date',
            'chamada' => 'required|array',
        ]);

        $diaLivre = app(CalendarioService::class)
            ->verificarDiaLivre($dados['data']);

        if ($diaLivre) {
            return response()->json([
                'erro' => 'Não é possível registrar chamada em dia livre.',
            ], 403);
        }

        foreach ($dados['chamada'] as $item) {
            Frequencia::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'disciplina_id' => $item['disciplina_id'],
                    'data' => $dados['data'], // Usa a data escolhida no calendário
                ],
                [
                    'presente' => $item['presente'],
                    'observacao' => $item['presente'] ? 'Presença' : 'Falta (Registro Manual)',
                ]
            );
        }

        return response()->json(['sucesso' => true]);
    }
}