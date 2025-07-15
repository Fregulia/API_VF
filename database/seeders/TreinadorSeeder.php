<?php

namespace Database\Seeders;

use App\Models\Treinador;
use App\Models\Esporte;
use Illuminate\Database\Seeder;

class TreinadorSeeder extends Seeder
{
    public function run(): void
    {
        $esportes = Esporte::all();
        
        Treinador::factory(15)->create([
            'esporte_id' => $esportes->random()->id
        ]);
    }
}