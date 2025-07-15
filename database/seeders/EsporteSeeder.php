<?php

namespace Database\Seeders;

use App\Models\Esporte;
use Illuminate\Database\Seeder;

class EsporteSeeder extends Seeder
{
    public function run(): void
    {
        Esporte::factory(10)->create();
    }
}