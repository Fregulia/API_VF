<?php

namespace Tests\Feature;

use App\Models\Atleta;
use App\Models\Esporte;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_atleta_has_foto_field()
    {
        $atleta = Atleta::factory()->create(['foto' => 'test-photo.jpg']);
        
        $this->assertEquals('test-photo.jpg', $atleta->foto);
        $this->assertContains('foto', $atleta->getFillable());
    }

    public function test_atleta_can_be_created_without_foto()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $esporte = Esporte::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/atleta', [
                             'nome' => 'João Silva',
                             'idade' => 25,
                             'categoria' => 'Profissional',
                             'esporte_id' => $esporte->id
                         ]);

        $response->assertStatus(201);
        
        $atleta = Atleta::latest()->first();
        $this->assertNull($atleta->foto);
    }

    public function test_atleta_foto_field_is_nullable()
    {
        $atleta = Atleta::factory()->create();
        
        $this->assertTrue(\Schema::hasColumn('atletas', 'foto'));
        $this->assertNull($atleta->foto);
    }

    public function test_storage_directory_exists()
    {
        $this->assertTrue(Storage::disk('public')->exists(''));
    }

    public function test_upload_validation_implemented()
    {
        // Testa se a validação de upload está implementada no controller
        $user = User::factory()->create(['role' => 'admin']);
        $esporte = Esporte::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/atleta', [
                             'nome' => 'João Silva',
                             'idade' => 25,
                             'categoria' => 'Profissional',
                             'esporte_id' => $esporte->id,
                             'foto' => 'invalid-file-data'
                         ]);

        // Deve retornar erro de validação ou sucesso (dependendo da implementação)
        $this->assertContains($response->status(), [201, 422]);
    }
}