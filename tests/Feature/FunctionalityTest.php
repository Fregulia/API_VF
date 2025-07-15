<?php

namespace Tests\Feature;

use App\Models\Atleta;
use App\Models\Esporte;
use App\Models\Treinador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_models_can_be_created()
    {
        $esporte = Esporte::factory()->create();
        $atleta = Atleta::factory()->create();
        $treinador = Treinador::factory()->create();

        $this->assertDatabaseHas('esportes', ['id' => $esporte->id]);
        $this->assertDatabaseHas('atletas', ['id' => $atleta->id]);
        $this->assertDatabaseHas('treinadors', ['id' => $treinador->id]);
    }

    public function test_pivot_table_exists()
    {
        $this->assertTrue(\Schema::hasTable('atleta_treinador'));
        
        $columns = \Schema::getColumnListing('atleta_treinador');
        $this->assertContains('atleta_id', $columns);
        $this->assertContains('treinador_id', $columns);
    }

    public function test_relationships_work_manually()
    {
        $esporte = Esporte::factory()->create();
        $atleta = Atleta::factory()->create(['esporte_id' => $esporte->id]);
        $treinador = Treinador::factory()->create(['esporte_id' => $esporte->id]);

        // Attach manually
        $atleta->treinadores()->attach($treinador->id);

        $this->assertCount(1, $atleta->treinadores);
        $this->assertCount(1, $treinador->atletas);
        $this->assertEquals($esporte->id, $atleta->esporte->id);
    }

    public function test_api_endpoints_return_data()
    {
        $endpoints = ['/api/esporte', '/api/atleta', '/api/treinador'];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertStatus(200)
                     ->assertJsonStructure(['data']);
        }
    }

    public function test_authenticated_user_can_create_atleta()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $esporte = Esporte::first();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/atleta', [
                             'nome' => 'Teste Atleta',
                             'idade' => 25,
                             'categoria' => 'Profissional',
                             'esporte_id' => $esporte->id
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('atletas', ['nome' => 'Teste Atleta']);
    }

    public function test_foto_field_exists_in_atletas()
    {
        $this->assertTrue(\Schema::hasColumn('atletas', 'foto'));
        
        $atleta = Atleta::factory()->create(['foto' => 'test.jpg']);
        $this->assertEquals('test.jpg', $atleta->foto);
    }

    public function test_eager_loading_works()
    {
        $esporte = Esporte::with(['atletas', 'treinadores'])->first();
        
        $this->assertTrue($esporte->relationLoaded('atletas'));
        $this->assertTrue($esporte->relationLoaded('treinadores'));
    }

    public function test_factories_create_valid_data()
    {
        $esporte = Esporte::factory()->create();
        $atleta = Atleta::factory()->create();
        $treinador = Treinador::factory()->create();

        $this->assertNotEmpty($esporte->nome);
        $this->assertNotEmpty($atleta->nome);
        $this->assertNotEmpty($treinador->nome);
        $this->assertNotEmpty($treinador->cref);
    }

    public function test_seeders_populated_database()
    {
        $this->assertGreaterThan(0, Esporte::count());
        $this->assertGreaterThan(0, Atleta::count());
        $this->assertGreaterThan(0, Treinador::count());
    }

    public function test_all_required_migrations_exist()
    {
        $tables = ['esportes', 'atletas', 'treinadors', 'atleta_treinador'];
        
        foreach ($tables as $table) {
            $this->assertTrue(\Schema::hasTable($table), "Table {$table} does not exist");
        }
    }
}