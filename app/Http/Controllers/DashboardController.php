<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Services\CalendarioService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->has_seen_intro) {
            return redirect()->route('intro');
        }

        // 1. CARREGAR DADOS GLOBAIS (Eventos e Feriados) UMA ÚNICA VEZ
        // Isso evita que o Model Disciplina faça queries repetidas.
        $inicioAno = Carbon::parse($user->ano_letivo_inicio ?? now()->startOfYear());
        $fimAno = Carbon::parse($user->ano_letivo_fim ?? now()->endOfYear());

        $folgasManuais = Evento::where('user_id', $user->id)
            ->whereBetween('data', [$inicioAno, $fimAno])
            ->whereIn('tipo', ['feriado', 'sem_aula'])
            ->pluck('data')
            ->map(fn($d) => Carbon::parse($d)->toDateString()) // Y-m-d
            ->toArray();

        // Cache de feriados estaduais (Simulando o Service para performance)
        $calendarioService = app(CalendarioService::class);
        $feriadosEstado = [];
        foreach (range($inicioAno->year, $fimAno->year) as $ano) {
            $lista = $calendarioService->obterFeriados($user->estado ?? 'BR', $ano);
            foreach ($lista as $f) {
                $feriadosEstado[] = Carbon::parse($f['data'])->toDateString();
            }
        }
        
        // Unimos todas as datas proibidas (folgas + feriados) no mesmo padrão Y-m-d
        $datasSemAula = array_unique(array_merge($folgasManuais, $feriadosEstado));

        // Converte para "set" (lookup instantâneo via isset)
        $datasSemAulaSet = array_fill_keys($datasSemAula, true);


        // 2. BUSCA DAS MATÉRIAS COM AGREGADOS
        $todasDisciplinas = $user->disciplinas()
            ->with(['horarios']) // Traz horários para calcular previsão
            ->withCount('frequencias as total_aulas_realizadas')
            ->withCount(['frequencias as total_faltas' => function ($query) {
                // Garante que só conta falta se presente for FALSE (exclui NULL se houver)
                $query->where('presente', false); 
            }])
            ->orderBy('nome', 'asc')
            ->get();

        // 3. CÁLCULOS EM MEMÓRIA (O "Hydrator")
        // Aqui preenchemos os atributos que a View vai usar, para ela não chamar o banco.
        $todasDisciplinas->each(function ($d) use ($datasSemAulaSet) {
            
            // A. Taxa de Presença (Evita chamar frequencias->count())
            if ($d->total_aulas_realizadas > 0) {
                $presencas = $d->total_aulas_realizadas - $d->total_faltas;
                $taxa = round(($presencas / $d->total_aulas_realizadas) * 100);
            } else {
                $taxa = 100;
            }
            // Força o atributo para a View não acionar o Accessor
            $d->setAttribute('taxa_presenca', $taxa);

            // B. Previsão de Aulas (Lógica movida do Model para cá para usar cache de Eventos)
            $previsao = $this->calcularAulasPrevistas($d, $datasSemAulaSet);
            $d->setAttribute('total_aulas_previstas_cache', $previsao);
        });


        // 4. ESTATÍSTICAS GERAIS (Query Única)
        $statsGerais = $user->frequencias()
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when presente = false then 1 end) as faltas')
            ->first();

        $totalAulasGeral = $statsGerais->total ?? 0;
        $totalFaltasGeral = $statsGerais->faltas ?? 0;
        $totalPresencasGeral = $totalAulasGeral - $totalFaltasGeral;

        // Lógica Visual
        $estadoVazio = $totalAulasGeral === 0;
        $porcentagemGlobal = $totalAulasGeral > 0 ? round(($totalPresencasGeral / $totalAulasGeral) * 100) : 0;
        
        $corGlobal = match (true) {
            $porcentagemGlobal < 75 => 'text-red-500',
            $porcentagemGlobal < 85 => 'text-yellow-500',
            default => 'text-emerald-500',
        };

        // Filtro em Memória (Rápido)
        $materiasEmRisco = $todasDisciplinas->filter(fn($d) => $d->total_aulas_realizadas > 0 && $d->taxa_presenca <= 75)->count();
        
        $disciplinasFiltradas = match ($request->filtro) {
            'hoje' => $todasDisciplinas->filter(fn($d) => $d->horarios->contains('dia_semana', now()->dayOfWeekIso)),
            'risco' => $todasDisciplinas->filter(fn($d) => $d->total_aulas_realizadas > 0 && $d->taxa_presenca <= 75),
            default => $todasDisciplinas
        };

        return view('dashboard', compact(
            'todasDisciplinas',
            'disciplinasFiltradas',
            'porcentagemGlobal', 
            'corGlobal', 
            'materiasEmRisco', 
            'totalPresencasGeral', 
            'estadoVazio'
        ));
    }

    /**
     * Função auxiliar otimizada que não faz queries
     */
    private function calcularAulasPrevistas($disciplina, array $datasSemAulaSet)
    {
        if (!$disciplina->data_inicio || !$disciplina->data_fim) {
            return 0;
        }

        $inicio = Carbon::parse($disciplina->data_inicio);
        $fim = Carbon::parse($disciplina->data_fim);
        $diasAula = $disciplina->horarios->pluck('dia_semana')->unique()->toArray();

        if (empty($diasAula)) {
            return 0;
        }

        $count = 0;
        // Loop simples, mas agora com lookup O(1)
        while ($inicio->lte($fim)) {
            if (in_array($inicio->dayOfWeekIso, $diasAula, true)) {
                $data = $inicio->toDateString(); // 'YYYY-MM-DD'
                if (!isset($datasSemAulaSet[$data])) {
                    $count++;
                }
            }
            $inicio->addDay();
        }

        return $count;
    }
}