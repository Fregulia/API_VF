<?php

namespace Tests\Unit;

use App\Models\Atleta;
use App\Models\Esporte;
use App\Models\Treinador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_esporte_factory_creates_valid_data()
    {
        $esporte = Esporte::factory()->create();

        $this->assertNotNull($esporte->nome);
        $this->assertNotNull($esporte->federacao);
        $this->assertIsString($esporte->nome);
        $this->assertIsString($esporte->federacao);
    }

    public function test_atleta_factory_creates_valid_data()
    {
        $atleta = Atleta::factory()->create();

        $this->assertNotNull($atleta->nome);
        $this->assertIsString($atleta->nome);
        $this->assertIsInt($atleta->idade);
        $this->assertGreaterThanOrEqual(16, $atleta->idade);
        $this->assertLessThanOrEqual(40, $atleta->idade);
        $this->assertNotNull($atleta->categoria);
        $this->assertContains($atleta->categoria, ['Juvenil', 'Adulto', 'Master', 'Profissional']);
    }

    public function test_treinador_factory_creates_valid_data()
    {
        $treinador = Treinador::factory()->create();

        $this->assertNotNull($treinador->nome);
        $this->assertNotNull($treinador->cref);
        $this->assertIsString($treinador->nome);
        $this->assertStringStartsWith('CREF-', $treinador->cref);
        $this->assertNotNull($treinador->especialidade);
        $this->assertContains($treinador->especialidade, ['Preparação Física', 'Técnico', 'Fisioterapia', 'Nutrição']);
    }

    public function test_factories_create_with_relationships()
    {
        $esporte = Esporte::factory()->create();
        $atleta = Atleta::factory()->create(['esporte_id' => $esporte->id]);
        $treinador = Treinador::factory()->create(['esporte_id' => $esporte->id]);

        $this->assertEquals($esporte->id, $atleta->esporte_id);
        $this->assertEquals($esporte->id, $treinador->esporte_id);
        $this->assertEquals($esporte->nome, $atleta->esporte->nome);
        $this->assertEquals($esporte->nome, $treinador->esporte->nome);
    }

    public function test_factories_create_multiple_records()
    {
        $esportes = Esporte::factory(5)->create();
        $atletas = Atleta::factory(10)->create();
        $treinadores = Treinador::factory(7)->create();

        $this->assertCount(5, $esportes);
        $this->assertCount(10, $atletas);
        $this->assertCount(7, $treinadores);
    }

    public function test_cref_uniqueness_in_factory()
    {
        $treinadores = Treinador::factory(10)->create();
        $crefs = $treinadores->pluck('cref')->toArray();

        $this->assertCount(10, array_unique($crefs));
    }
}