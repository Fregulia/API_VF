<?php

namespace Database\Factories;

use App\Models\Esporte;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreinadorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cref' => fake()->unique()->numerify('CREF-###-###'),
            'especialidade' => fake()->randomElement(['Preparação Física', 'Técnico', 'Fisioterapia', 'Nutrição']),
            'esporte_id' => Esporte::factory(),
        ];
    }
}