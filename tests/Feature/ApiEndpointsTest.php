<?php

namespace Tests\Feature;

use App\Models\Atleta;
use App\Models\Esporte;
use App\Models\Treinador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_esporte_index_returns_with_relationships()
    {
        $response = $this->getJson('/api/esporte');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'nome',
                             'federacao',
                             'descricao',
                             'atletas',
                             'treinadores'
                         ]
                     ]
                 ]);
    }

    public function test_atleta_index_returns_with_relationships()
    {
        $response = $this->getJson('/api/atleta');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'nome',
                             'idade',
                             'categoria',
                             'esporte',
                             'treinadores'
                         ]
                     ]
                 ]);
    }

    public function test_treinador_index_returns_with_relationships()
    {
        $response = $this->getJson('/api/treinador');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'nome',
                             'cref',
                             'especialidade',
                             'esporte',
                             'atletas'
                         ]
                     ]
                 ]);
    }

    public function test_esporte_show_returns_with_relationships()
    {
        $esporte = Esporte::first();

        $response = $this->getJson("/api/esporte/{$esporte->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'nome',
                         'federacao',
                         'atletas',
                         'treinadores'
                     ]
                 ]);
    }

    public function test_atleta_create_with_relationships()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $esporte = Esporte::first();
        $treinadores = Treinador::take(2)->pluck('id')->toArray();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/atleta', [
                             'nome' => 'Novo Atleta',
                             'idade' => 22,
                             'categoria' => 'Juvenil',
                             'esporte_id' => $esporte->id,
                             'treinadores' => $treinadores
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'nome',
                         'esporte',
                         'treinadores'
                     ]
                 ]);

        $atleta = Atleta::latest()->first();
        $this->assertCount(1, $atleta->treinadores);
    }

    public function test_atleta_update_syncs_relationships()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $atleta = Atleta::first();
        $novosTreinadores = Treinador::take(2)->pluck('id')->toArray();

        $response = $this->actingAs($user, 'sanctum')
                         ->putJson("/api/atleta/{$atleta->id}", [
                             'nome' => 'Nome Atualizado',
                             'treinadores' => $novosTreinadores
                         ]);

        $response->assertStatus(200);

        $atleta->refresh();
        $this->assertEquals('Nome Atualizado', $atleta->nome);
        $this->assertCount(2, $atleta->treinadores);
    }

    public function test_public_can_access_get_endpoints()
    {
        $endpoints = ['/api/esporte', '/api/atleta', '/api/treinador'];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertStatus(200);
        }
    }

    public function test_unauthenticated_cannot_create_resources()
    {
        $endpoints = [
            ['POST', '/api/esporte', ['nome' => 'Test']],
            ['POST', '/api/atleta', ['nome' => 'Test']],
            ['POST', '/api/treinador', ['nome' => 'Test', 'cref' => 'TEST-123']]
        ];

        foreach ($endpoints as [$method, $url, $data]) {
            $response = $this->json($method, $url, $data);
            $response->assertStatus(401);
        }
    }
}