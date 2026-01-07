<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisciplinaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a lista de disciplinas é renderizada.
     */
    public function test_lista_de_disciplinas_pode_ser_renderizada(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/disciplinas'); // Verifique se sua rota é '/disciplinas' ou '/dashboard'

        $response->assertStatus(200);
    }

    /**
     * Testa se uma nova disciplina pode ser criada.
     */
    public function test_usuario_pode_criar_disciplina(): void
    {
        $user = User::factory()->create();

        $dadosDisciplina = [
            'nome' => 'Matemática Avançada',
            'cor' => '#FF5733',
            'data_inicio' => '2024-02-01',
            'data_fim' => '2024-12-15',
        ];

        $response = $this
            ->actingAs($user)
            ->post('/disciplinas', $dadosDisciplina); // Rota de store

        // Verifica se redirecionou (geralmente para o index ou dashboard)
        $response->assertRedirect(); 
        
        // Verifica se salvou no banco
        $this->assertDatabaseHas('disciplinas', [
            'nome' => 'Matemática Avançada',
            'user_id' => $user->id, // Garante que vinculou ao usuário certo
        ]);
    }

    /**
     * Testa validação: não pode criar sem nome.
     */
    public function test_nao_pode_criar_disciplina_sem_nome(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/disciplinas', [
                'cor' => '#000000',
            ]);

        $response->assertSessionHasErrors('nome');
    }

    /**
     * Testa privacidade: Usuário A não vê disciplina do Usuário B.
     */
    public function test_usuario_nao_ve_disciplinas_de_outros(): void
    {
        // Cria Usuário A e sua disciplina
        $userA = User::factory()->create();
        $disciplinaA = Disciplina::factory()->create(['user_id' => $userA->id, 'nome' => 'Segredo do A']);

        // Cria Usuário B
        $userB = User::factory()->create();

        // Loga como B e tenta ver a lista
        $response = $this
            ->actingAs($userB)
            ->get('/disciplinas');

        // Garante que o texto "Segredo do A" NÃO aparece na tela do B
        $response->assertDontSee('Segredo do A');
    }
}