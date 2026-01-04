<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o CRUD básico: Criar um evento.
     */
    public function test_usuario_pode_criar_dia_livre(): void
    {
        $user = User::factory()->create();

        $dados = [
            'titulo' => 'Carnaval',
            'data' => '2024-02-10',
            'tipo' => 'feriado',
        ];

        $response = $this->actingAs($user)
            ->post(route('eventos.store'), $dados);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('eventos', [
            'titulo' => 'Carnaval',
            'user_id' => $user->id,
        ]);
    
        // 2. Busca o registro e verifica a data formatada
        $evento = \App\Models\Evento::where('user_id', $user->id)->first();
        
        // Compara apenas a parte da data (Y-m-d), ignorando o horário
        $this->assertEquals('2024-02-10', \Carbon\Carbon::parse($evento->data)->format('Y-m-d'));
    }

    /**
     * TESTE DE INTEGRAÇÃO: O dia livre bloqueia a chamada?
     */
    public function test_nao_permite_registrar_frequencia_em_dia_livre(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Cria uma disciplina
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);
        
        // 2. Cria um Evento (Feriado) para HOJE
        $hoje = now()->format('Y-m-d');
        Evento::factory()->create([
            'user_id' => $user->id,
            'data' => $hoje,
            'titulo' => 'Santo Padroeiro',
            'tipo' => 'feriado'
        ]);

        // 3. Tenta registrar falta nesse dia via API
        $response = $this->postJson('/api/registrar-chamada', [
            'data' => $hoje,
            'chamada' => [
                ['disciplina_id' => $disciplina->id, 'presente' => false]
            ]
        ]);

        // 4. Deve falhar (Erro 403 - Proibido)
        $response->assertStatus(403);
        $response->assertJsonFragment(['erro' => 'Não é possível registrar chamada em dia livre.']);
        
        // Garante que NÃO salvou a falta no banco
        $this->assertDatabaseMissing('frequencias', [
            'data' => $hoje,
            'user_id' => $user->id
        ]);
    }
}