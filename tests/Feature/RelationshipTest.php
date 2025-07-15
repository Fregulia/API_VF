<?php

namespace Tests\Feature;

use App\Models\Atleta;
use App\Models\Esporte;
use App\Models\Treinador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_esporte_has_many_atletas()
    {
        $esporte = Esporte::factory()->create();
        $atletas = Atleta::factory(3)->create(['esporte_id' => $esporte->id]);

        $this->assertCount(3, $esporte->atletas);
        $this->assertTrue($esporte->atletas->contains($atletas->first()));
    }

    public function test_esporte_has_many_treinadores()
    {
        $esporte = Esporte::factory()->create();
        $treinadores = Treinador::factory(2)->create(['esporte_id' => $esporte->id]);

        $this->assertCount(2, $esporte->treinadores);
        $this->assertTrue($esporte->treinadores->contains($treinadores->first()));
    }

    public function test_atleta_belongs_to_esporte()
    {
        $esporte = Esporte::factory()->create();
        $atleta = Atleta::factory()->create(['esporte_id' => $esporte->id]);

        $this->assertEquals($esporte->id, $atleta->esporte->id);
        $this->assertEquals($esporte->nome, $atleta->esporte->nome);
    }

    public function test_atleta_belongs_to_many_treinadores()
    {
        $atleta = Atleta::factory()->create();
        $treinadores = Treinador::factory(3)->create();
        
        $atleta->treinadores()->attach($treinadores->pluck('id'));

        $this->assertCount(3, $atleta->treinadores);
        $this->assertTrue($atleta->treinadores->contains($treinadores->first()));
    }

    public function test_treinador_belongs_to_esporte()
    {
        $esporte = Esporte::factory()->create();
        $treinador = Treinador::factory()->create(['esporte_id' => $esporte->id]);

        $this->assertEquals($esporte->id, $treinador->esporte->id);
        $this->assertEquals($esporte->nome, $treinador->esporte->nome);
    }

    public function test_treinador_belongs_to_many_atletas()
    {
        $treinador = Treinador::factory()->create();
        $atletas = Atleta::factory(2)->create();
        
        $treinador->atletas()->attach($atletas->pluck('id'));

        $this->assertCount(2, $treinador->atletas);
        $this->assertTrue($treinador->atletas->contains($atletas->first()));
    }

    public function test_api_returns_relationships()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/esporte');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'nome',
                             'federacao',
                             'atletas',
                             'treinadores'
                         ]
                     ]
                 ]);
    }
}