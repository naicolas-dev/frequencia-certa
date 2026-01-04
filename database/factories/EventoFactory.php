<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titulo' => 'Feriado',
            'data' => now()->format('Y-m-d'),
            'tipo' => 'feriado', // ou 'sem_aula'
            'descricao' => 'Teste de feriado',
        ];
    }
}