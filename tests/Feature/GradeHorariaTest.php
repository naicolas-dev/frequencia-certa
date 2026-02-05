<?php

namespace Tests\Feature;

use App\Models\Disciplina;
use App\Models\GradeHoraria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeHorariaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se consegue visualizar a página de edição da grade de uma disciplina.
     */
    public function test_pode_ver_tela_de_gerenciar_grade(): void
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('grade.index', $disciplina->id));

        $response->assertStatus(200);
        $response->assertSee($disciplina->nome);
    }

    /**
     * Testa o cadastro de um horário válido.
     */
    public function test_pode_adicionar_horario_valido(): void
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        $dados = [
            'disciplina_id' => $disciplina->id,
            'dia_semana' => 1, // Segunda-feira
            'horario_inicio' => '08:00',
            'horario_fim' => '10:00',
        ];

        $response = $this->actingAs($user)
            ->post(route('grade.store'), $dados);

        // Deve redirecionar de volta (back)
        $response->assertRedirect();

        // Verifica se salvou no banco
        $this->assertDatabaseHas('grade_horarias', [
            'user_id' => $user->id,
            'disciplina_id' => $disciplina->id,
            'horario_inicio' => '08:00',
            'horario_fim' => '10:00',
        ]);
    }

    /**
     * O TESTE MAIS IMPORTANTE: Impede conflito de horário.
     */
    public function test_nao_permite_horario_conflitante(): void
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        // 1. Cria um horário existente: Segunda, 08:00 às 10:00
        GradeHoraria::create([
            'user_id' => $user->id,
            'disciplina_id' => $disciplina->id,
            'dia_semana' => 1,
            'horario_inicio' => '08:00',
            'horario_fim' => '10:00',
        ]);

        // 2. Tenta criar outro horário que SOBREPÕE: Segunda, 09:00 às 11:00
        // (Começa antes do outro terminar)
        $dadosConflitantes = [
            'disciplina_id' => $disciplina->id,
            'dia_semana' => 1,
            'horario_inicio' => '09:00',
            'horario_fim' => '11:00',
        ];

        $response = $this->actingAs($user)
            ->from(route('grade.index', $disciplina->id)) // Simula estar na página anterior
            ->post(route('grade.store'), $dadosConflitantes);

        // 3. Verifica:
        // - Redirecionou de volta
        $response->assertRedirect(route('grade.index', $disciplina->id));

        // - A sessão tem uma mensagem toast de erro (baseado no seu controller)
        $response->assertSessionHas('toast');

        // - NÃO salvou o segundo registro no banco (deve ter apenas 1)
        $this->assertDatabaseCount('grade_horarias', 1);
    }

    /**
     * Testa validação lógica: Fim deve ser depois do Início.
     */
    public function test_nao_permite_horario_final_antes_do_inicial(): void
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        $dadosErrados = [
            'disciplina_id' => $disciplina->id,
            'dia_semana' => 1,
            'horario_inicio' => '10:00',
            'horario_fim' => '08:00', // Errado!
        ];

        $response = $this->actingAs($user)
            ->post(route('grade.store'), $dadosErrados);

        $response->assertSessionHasErrors('horario_fim');
    }

    /**
     * Testa exclusão.
     */
    public function test_pode_excluir_horario(): void
    {
        $user = User::factory()->create();
        $disciplina = Disciplina::factory()->create(['user_id' => $user->id]);

        $horario = GradeHoraria::create([
            'user_id' => $user->id,
            'disciplina_id' => $disciplina->id,
            'dia_semana' => 1,
            'horario_inicio' => '14:00',
            'horario_fim' => '16:00',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('grade.destroy', $horario->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('grade_horarias', ['id' => $horario->id]);
    }
}