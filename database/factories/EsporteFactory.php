<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EsporteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->randomElement(['Futebol', 'Basquete', 'Vôlei', 'Natação', 'Atletismo', 'Tênis']),
            'federacao' => fake()->company() . ' Federation',
            'descricao' => fake()->sentence(10),
        ];
    }
}