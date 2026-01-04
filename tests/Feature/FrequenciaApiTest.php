<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\GradeHoraria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class FrequenciaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_retorna_aulas_agendadas_para_o_dia(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Segunda-feira segura deste ano
        $dataTeste = Carbon::createFromDate(now()->year, 6, 1)->next('Monday');
        
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id, 'nome' => 'Física Quântica']);
        
        GradeHoraria::create([
            'user_id' => $user->id,
            'disciplina_id' => $disciplina->id,
            'dia_semana' => 1, 
            'horario_inicio' => '08:00',
            'horario_fim' => '10:00',
        ]);

        $response = $this->getJson('/api/buscar-aulas?data=' . $dataTeste->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'nome' => 'Física Quântica',
                'horario' => '08:00', // CORRIGIDO: Sem segundos
                'presente' => true,
            ]);
    }

    public function test_api_salva_falta_corretamente(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);
        $hoje = now()->format('Y-m-d');

        $dadosDoModal = [
            'data' => $hoje,
            'chamada' => [
                [
                    'disciplina_id' => $disciplina->id,
                    'presente' => false 
                ]
            ]
        ];

        $response = $this->postJson('/api/registrar-chamada', $dadosDoModal);

        $response->assertStatus(200);

        // CORRIGIDO: Verificação mais robusta
        $this->assertDatabaseHas('frequencias', [
            'user_id' => $user->id,
            'disciplina_id' => $disciplina->id,
            'presente' => 0, // SQLite/MySQL usam 0 para false
        ]);
        
        // Garante que a data está certa ignorando horas/segundos
        $registro = \App\Models\Frequencia::first();
        $this->assertEquals($hoje, $registro->data->format('Y-m-d'));
    }

    public function test_api_valida_dados_incompletos(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/registrar-chamada', [
            'data' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422); 
        $response->assertJsonValidationErrors(['chamada']);
    }
}