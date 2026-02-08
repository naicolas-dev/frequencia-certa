<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\Frequencia;
use App\Models\User;
use App\Services\DisciplinaStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisciplinaStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_disciplina_stats_service_enrich_creates_stats(): void
    {
        // 1. Setup: Create a user and a discipline
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create([
            'user_id' => $user->id,
            'nome' => 'Matemática'
        ]);

        // 2. Setup: Create attendance records (frequencias)
        // 3 classes carried out, 1 absent
        Frequencia::factory()->count(2)->create([
            'disciplina_id' => $disciplina->id,
            'presente' => true,
        ]);

        Frequencia::factory()->create([
            'disciplina_id' => $disciplina->id,
            'presente' => false,
        ]);

        // 3. Act: Fetch the discipline with the same query logic as the controller
        $disciplinas = $user->disciplinas()
            ->with(['horarios', 'frequencias'])
            ->withCount([
                'frequencias as total_aulas_realizadas',
                'frequencias as total_faltas' => function ($q) {
                    $q->where('presente', false);
                }
            ])
            ->get();

        $this->assertCount(1, $disciplinas);
        $disciplinaCarregada = $disciplinas->first();

        // 4. Assert: Check initial counts from database
        $this->assertEquals(3, $disciplinaCarregada->total_aulas_realizadas);
        $this->assertEquals(1, $disciplinaCarregada->total_faltas);

        // 5. Act: Use the service to enrich
        $statsService = app(DisciplinaStatsService::class);
        $statsService->enrichWithStats($disciplinas, $user);

        // 6. Assert: Check if the service appended the cached attribute
        // Note: The specific value depends on the service logic and mocking/config,
        // but for now we just want to ensure the attribute is set (not null).
        // Since it relies on cache or calculations, we check existence.

        $this->assertTrue(
            array_key_exists('total_aulas_previstas_cache', $disciplinaCarregada->getAttributes()) ||
            isset($disciplinaCarregada->total_aulas_previstas_cache),
            'The attribute total_aulas_previstas_cache should be present after enrichment.'
        );
    }
}
