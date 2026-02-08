<?php

namespace Database\Factories;

use App\Models\Disciplina;
use App\Models\Frequencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Frequencia>
 */
class FrequenciaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Frequencia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'disciplina_id' => Disciplina::factory(),
            'data' => $this->faker->date(),
            'horario' => $this->faker->time('H:i'),
            'presente' => $this->faker->boolean(),
            'observacao' => $this->faker->optional()->sentence(),
        ];
    }
}
