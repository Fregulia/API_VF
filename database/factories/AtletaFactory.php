<?php

namespace Database\Factories;

use App\Models\Esporte;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtletaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'idade' => fake()->numberBetween(16, 40),
            'categoria' => fake()->randomElement(['Juvenil', 'Adulto', 'Master', 'Profissional']),
            'esporte_id' => Esporte::factory(),
        ];
    }
}