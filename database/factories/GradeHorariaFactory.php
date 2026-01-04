<?php

namespace Database\Factories;

use App\Models\Disciplina;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeHorariaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'disciplina_id' => Disciplina::factory(),
            'dia_semana' => $this->faker->numberBetween(1, 6), // Seg a Sab
            'horario_inicio' => '08:00',
            'horario_fim' => '10:00',
        ];
    }
}