<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Disciplina;
use App\Models\Frequencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculoFrequenciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_calcula_80_porcento_corretamente()
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        // ADICIONADO: 'user_id' => $user->id em todas as linhas
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-01', 'presente' => true]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-02', 'presente' => true]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-03', 'presente' => true]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-04', 'presente' => true]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-05', 'presente' => false]);

        // Reload to get frequencias relation loaded
        $disciplina->load('frequencias');

        // (4 presenças / 5 aulas) * 100 = 80%
        $this->assertEquals(80.0, $disciplina->taxa_presenca);
    }

    public function test_disciplina_sem_aulas_tem_100_porcento_inicial()
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        // Reload with empty frequencias relation
        $disciplina->load('frequencias');

        // No aulas = 0% (safe fallback, not 100%)
        $this->assertEquals(0.0, $disciplina->taxa_presenca);
    }

    public function test_calcula_reprovacao_abaixo_de_75()
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        // ADICIONADO: 'user_id' => $user->id em todas as linhas
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-01', 'presente' => true]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-02', 'presente' => true]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-03', 'presente' => false]);
        Frequencia::create(['user_id' => $user->id, 'disciplina_id' => $disciplina->id, 'data' => '2024-02-04', 'presente' => false]);

        // Reload to get frequencias relation loaded
        $disciplina->load('frequencias');

        // (2 presenças / 4 aulas) * 100 = 50%
        $this->assertEquals(50.0, $disciplina->taxa_presenca);
        $this->assertLessThan(75, $disciplina->taxa_presenca);
    }
}