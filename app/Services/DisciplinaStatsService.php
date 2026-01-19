<?php

namespace App\Services;

use App\Models\Disciplina;
use App\Models\Evento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Service responsible for batch-computing statistics for Disciplinas
 * to avoid N+1 query problems.
 * 
 * This service precomputes:
 * - taxa_presenca (attendance rate)
 * - total_aulas_previstas (projected total classes)
 * 
 * Using O(1) queries instead of N queries per discipline.
 */
class DisciplinaStatsService
{
    protected CalendarioService $calendarioService;

    public function __construct(CalendarioService $calendarioService)
    {
        $this->calendarioService = $calendarioService;
    }

    /**
     * Main entry point: enriches a collection of Disciplinas with computed stats.
     * 
     * This method computes total_aulas_previstas for each discipline using
     * a single batch query for Eventos, avoiding N+1.
     * 
     * @param Collection $disciplinas Collection of Disciplina models
     * @param User $user The authenticated user
     * @return void (modifies disciplinas in-place via setAttribute)
     */
    public function enrichWithStats(Collection $disciplinas, User $user): void
    {
        if ($disciplinas->isEmpty()) {
            return;
        }

        // Compute projected classes in batch
        $this->computeAulasPrevistas($disciplinas, $user);
    }

    /**
     * Batch-computes total_aulas_previstas for all disciplines.
     * 
     * Query savings: 1 query total instead of N queries.
     * 
     * @param Collection $disciplinas
     * @param User $user
     * @return void
     */
    protected function computeAulasPrevistas(Collection $disciplinas, User $user): void
    {
        // Find the overall date range across all disciplines
        $minInicio = $disciplinas->min('data_inicio');
        $maxFim = $disciplinas->max('data_fim');

        if (!$minInicio || !$maxFim) {
            // No valid date ranges, set all to 0
            $disciplinas->each(fn($d) => $d->setAttribute('total_aulas_previstas_cache', 0));
            return;
        }

        // 1️⃣ SINGLE QUERY: Fetch all Eventos for the user in the entire date range
        $eventosDatas = Evento::where('user_id', $user->id)
            ->whereBetween('data', [$minInicio, $maxFim])
            ->whereIn('tipo', ['feriado', 'sem_aula'])
            ->pluck('data')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->flip() // Convert to associative array for O(1) lookup
            ->toArray();

        // 2️⃣ Fetch holidays from CalendarioService (cached)
        $inicio = Carbon::parse($minInicio);
        $fim = Carbon::parse($maxFim);
        $anos = range($inicio->year, $fim->year);
        $feriadosDatas = [];
        
        foreach ($anos as $ano) {
            $lista = $this->calendarioService->obterFeriados($user->estado ?? 'BR', $ano);
            foreach ($lista as $f) {
                $feriadosDatas[$f['data']] = true; // Use array as set for O(1) lookup
            }
        }

        // 3️⃣ Compute for each discipline (all in memory, no more queries)
        $disciplinas->each(function ($disciplina) use ($eventosDatas, $feriadosDatas) {
            $total = $this->calcularAulasPrevistasSemQuery(
                $disciplina, 
                $eventosDatas, 
                $feriadosDatas
            );
            
            $disciplina->setAttribute('total_aulas_previstas_cache', $total);
        });
    }

    /**
     * Pure computation method - NO DATABASE QUERIES.
     * 
     * Calculates projected classes for a single discipline using
     * pre-loaded event and holiday data.
     * 
     * @param Disciplina $disciplina
     * @param array $eventosDatas Associative array of date strings (Y-m-d => true)
     * @param array $feriadosDatas Associative array of date strings
     * @return int
     */
    protected function calcularAulasPrevistasSemQuery(
        Disciplina $disciplina, 
        array $eventosDatas, 
        array $feriadosDatas
    ): int {
        if (!$disciplina->data_inicio || !$disciplina->data_fim) {
            return 0;
        }

        $inicio = Carbon::parse($disciplina->data_inicio);
        $fim = Carbon::parse($disciplina->data_fim);

        // Get class days from already-loaded horarios relationship
        if (!$disciplina->relationLoaded('horarios')) {
            \Log::warning("DisciplinaStatsService: horarios not preloaded for Disciplina #{$disciplina->id}");
            return 0;
        }

        $diasAula = $disciplina->horarios->pluck('dia_semana')->unique()->toArray();

        if (empty($diasAula)) {
            return 0;
        }

        // Count valid class days
        $totalAulas = 0;
        $atual = $inicio->copy();

        while ($atual->lte($fim)) {
            if (in_array($atual->dayOfWeekIso, $diasAula, true)) {
                $dataStr = $atual->toDateString();
                
                // Check if it's NOT a manual day off AND NOT a holiday
                if (!isset($eventosDatas[$dataStr]) && !isset($feriadosDatas[$dataStr])) {
                    $totalAulas++;
                }
            }
            $atual->addDay();
        }

        return $totalAulas;
    }
}
