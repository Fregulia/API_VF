<?php

namespace Database\Seeders;

use App\Models\Atleta;
use App\Models\Esporte;
use App\Models\Treinador;
use Illuminate\Database\Seeder;

class AtletaSeeder extends Seeder
{
    public function run(): void
    {
        $esportes = Esporte::all();
        $treinadores = Treinador::all();
        
        Atleta::factory(20)->create([
            'esporte_id' => $esportes->random()->id
        ])->each(function ($atleta) use ($treinadores) {
            $atleta->treinadores()->attach($treinadores->random(rand(1, 3))->pluck('id'));
        });
    }
}