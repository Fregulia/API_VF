<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_all_resources()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/esporte', [
            'nome' => 'Futebol',
            'federacao' => 'CBF',
            'descricao' => 'Esporte popular'
        ]);

        $response->assertStatus(201);
    }

    public function test_manager_can_access_atleta_and_treinador()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $token = $manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/atleta', [
            'nome' => 'João Silva',
            'idade' => 25,
            'categoria' => 'Profissional'
        ]);

        $response->assertStatus(201);
    }

    public function test_manager_cannot_access_esporte_creation()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $token = $manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/esporte', [
            'nome' => 'Futebol',
            'federacao' => 'CBF'
        ]);

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_create_resources()
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/atleta', [
            'nome' => 'João Silva',
            'idade' => 25
        ]);

        $response->assertStatus(403);
    }

    public function test_public_can_access_get_routes()
    {
        $response = $this->getJson('/api/esporte');
        $response->assertStatus(200);

        $response = $this->getJson('/api/atleta');
        $response->assertStatus(200);

        $response = $this->getJson('/api/treinador');
        $response->assertStatus(200);
    }
}