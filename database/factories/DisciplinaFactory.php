<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disciplina>
 */
class DisciplinaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Cria um usuário automaticamente se não for passado
            'nome' => $this->faker->words(2, true), // Gera nomes como "Matemática", "História"
            'cor' => $this->faker->hexColor(),
            'data_inicio' => now(),
            'data_fim' => now()->addMonths(6),
            'carga_horaria_total' => 60,
            'porcentagem_minima' => 75,
        ];
    }
}