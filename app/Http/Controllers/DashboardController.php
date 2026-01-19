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


        // 3. USE SERVICE FOR BATCH COMPUTATION (Prevents N+1)
        $statsService = app(\App\Services\DisciplinaStatsService::class);
        $statsService->enrichWithStats($todasDisciplinas, $user);

        // Additional computation for taxa_presenca (using already-loaded counts)
        $todasDisciplinas->each(function ($d) {
            // Taxa de Presença from preloaded counts
            if ($d->total_aulas_realizadas > 0) {
                $presencas = $d->total_aulas_realizadas - $d->total_faltas;
                $taxa = round(($presencas / $d->total_aulas_realizadas) * 100);
            } else {
                $taxa = 0; // Changed: return 0 instead of 100 when no classes
            }
            // Force attribute so View doesn't trigger accessor
            $d->setAttribute('taxa_presenca', $taxa);
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

        // 5. LÓGICA DE APRESENTAÇÃO (Greeting & Tips)
        $hora = now()->hour;
        if ($hora < 12) { $saudacao = 'Bom dia'; }
        elseif ($hora < 18) { $saudacao = 'Boa tarde'; }
        else { $saudacao = 'Boa noite'; }

        $mensagensPorHora = [
            0 => 'Já deu por hoje 🙂 — descansar também é produtividade.',
            1 => 'Hora de desligar um pouco. Um bom sono melhora seu rendimento.',
            2 => 'Sono é parte do progresso. Seu eu de amanhã agradece.',
            3 => 'Tá bem tarde… cuida de você. Amanhã é um novo dia.',
            4 => 'Quase amanhecendo. Que tal se preparar pra não correr depois?',
            5 => 'Um novo começo chegando 🌅 Ajuste o ritmo e vai com calma.',
            6 => 'Bom começo de dia! Presença hoje faz diferença no final do semestre.',
            7 => 'Organiza o dia rapidinho e evita correria mais tarde.',
            8 => 'Primeiras aulas, primeira chance de mandar bem. Bora marcar presença?',
            9 => 'Mantém o ritmo: consistência é o que dá resultado.',
            10 => 'Cada aula conta. Confere sua presença e segue firme.',
            11 => 'Último gás da manhã 💪 Foco no que importa.',
            12 => 'Pausa merecida! Já aproveita e confirma sua presença.',
            13 => 'De volta aos estudos: calma, atenção e presença.',
            14 => 'Ainda dá tempo de virar o jogo hoje. Bora manter a frequência?',
            15 => 'Vai no constante: consistência vence a pressa.',
            16 => 'Olho na frequência 👀 O que você garante hoje evita dor de cabeça depois.',
            17 => 'Final da tarde chegando. Fecha o dia com presença em dia.',
            18 => 'Encerrando? Dá uma olhada na chamada antes de sair.',
            19 => 'Se organizar agora poupa estresse amanhã.',
            20 => 'Revisar hoje é se agradecer amanhã. 😉',
            21 => 'Última checagem do dia: tudo certo na frequência?',
            22 => 'Fechando o dia com responsabilidade. Boa!',
            23 => 'Hora de descansar 🌙 Amanhã continua — com mais uma presença.'
        ];

        $diaHoje = now()->dayOfWeekIso;
        $temAulaHoje = $todasDisciplinas->contains(function($d) use ($diaHoje) {
            return $d->horarios->contains('dia_semana', $diaHoje);
        });

        if ($todasDisciplinas->isEmpty()) {
            $fraseMotivacional = 'Comece adicionando suas matérias para montar a grade 🚀';
        } elseif (!$temAulaHoje) {
            $fraseMotivacional = 'Hoje não há aulas programadas. Aproveite o descanso 😌';
        } elseif ($materiasEmRisco > 0) {
            $fraseMotivacional = '⚠️ Atenção: você tem matérias com frequência baixa. Foco total!';
        } else {
            $fraseMotivacional = $mensagensPorHora[$hora] ?? 'Bons estudos!';
        }

        // 6. GAMIFICATION DATA
        $user->load('badges'); // Eager load para evitar N+1 na view
        
        $streak = $user->current_streak;
        $badgesCount = $user->badges->count();
        $hoje = Carbon::now()->startOfDay();
        $ultimoRegistro = $user->last_streak_date ? Carbon::parse($user->last_streak_date)->startOfDay() : null;
        $marcouHoje = $ultimoRegistro && $ultimoRegistro->equalTo($hoje);
        $dateString = $hoje->toDateString();

        $medalhasHoje = $user->badges->filter(function($badge) use ($hoje) {
            return Carbon::parse($badge->pivot->earned_at)->startOfDay()->equalTo($hoje);
        })->values();

        return view('dashboard', compact(
            'todasDisciplinas',
            'disciplinasFiltradas',
            'porcentagemGlobal', 
            'corGlobal', 
            'materiasEmRisco', 
            'totalPresencasGeral', 
            'estadoVazio',
            'saudacao',
            'fraseMotivacional',
            'temAulaHoje',
            'streak',
            'badgesCount',
            'marcouHoje',
            'dateString',
            'medalhasHoje'
        ));
    }

}