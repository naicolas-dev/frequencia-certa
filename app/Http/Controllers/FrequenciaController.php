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

        $request->validate([
        'data' => [
            'required', 
            'date',
            'after_or_equal:' . now()->startOfYear()->format('Y-m-d'), // >= 01/01/202X
            'before_or_equal:' . now()->endOfYear()->format('Y-m-d'),   // <= 31/12/202X]
        ],
        ]);

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
            ->whereHas('disciplina', function($query) use ($dataAlvo) {
                $query->where(function($q) use ($dataAlvo) {
                    $q->whereNull('data_inicio')
                        ->orWhere('data_inicio', '<=', $dataAlvo);
            })->where(function($q) use ($dataAlvo) {
                $q->whereNull('data_fim')
                    ->orWhere('data_fim', '>=', $dataAlvo);
            });
            })
            ->with('disciplina')
            ->orderBy('horario_inicio', 'asc')
            ->get();

        // 2. Busca o Histórico: O que já foi gravado nessa data?
        $historico = Frequencia::where('user_id', Auth::id())
            ->whereDate('data', $dataAlvo)
            ->get();

        // 3. Mescla os dois: Grade + O que foi marcado
        $resultado = $grade->map(function($aula) use ($historico) {
            $registro = $historico->first(function($h) use ($aula) {
                return $h->disciplina_id == $aula->disciplina->id 
                    && substr($h->horario, 0, 5) == substr($aula->horario_inicio, 0, 5);
            });

            return [
                'disciplina_id' => $aula->disciplina->id,
                'nome' => $aula->disciplina->nome,
                'cor' => $aula->disciplina->cor,
                'horario' => $aula->horario_inicio,
                'horario_fim' => $aula->horario_fim,
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
            'chamada.*.disciplina_id' => 'required|exists:disciplinas,id',
            'chamada.*.presente' => 'required|boolean',
            'chamada.*.horario' => 'required',
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
                    'horario' => $item['horario'],
                ],
                [
                    'presente' => $item['presente'],
                    'observacao' => $item['presente'] ? 'Presença' : 'Falta (Registro Manual)',
                ]
            );
        }

        return response()->json(['sucesso' => true]);
    }

    public function historico(Request $request)
    {
        // Inicia a query focada no utilizador logado
        $query = \App\Models\Frequencia::where('user_id', Auth::id())
            ->with('disciplina') // Traz a disciplina para não pesar o banco
            ->orderBy('data', 'desc')
            ->orderBy('created_at', 'desc');

        // 1. Filtro por Disciplina
        if ($request->filled('disciplina_id')) {
            $query->where('disciplina_id', $request->disciplina_id);
        }

        // 2. Filtro por Data Início
        if ($request->filled('data_inicio')) {
            $query->whereDate('data', '>=', $request->data_inicio);
        }

        // 3. Filtro por Data Fim
        if ($request->filled('data_fim')) {
            $query->whereDate('data', '<=', $request->data_fim);
        }

        // 4. Filtro por Status (Presença ou Falta)
        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('presente', $request->status);
        }

        // Paginação (15 itens por página)
        // O ->withQueryString() mantém os filtros ao clicar na página 2
        $historico = $query->paginate(15)->withQueryString();

        // Carrega todas as disciplinas para preencher o <select> de filtro
        $disciplinas = Auth::user()->disciplinas()->orderBy('nome')->get();

        return view('frequencia.historico', compact('historico', 'disciplinas'));
    }
}