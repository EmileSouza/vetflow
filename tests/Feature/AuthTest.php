<?php

namespace Tests\Feature;

use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_veterinario_consegue_fazer_login()
    {
        // 1. Cria um veterinário no banco (senha criptografada automaticamente pelo Model)
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        // 2. Tenta fazer login
        $response = $this->postJson('/api/login', [
            'email' => 'carlos@vet.com',
            'password' => 'senha123'
        ]);

        // 3. Deve retornar sucesso e um token
        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'veterinario']);
    }

    public function test_login_falha_com_senha_errada()
    {
        Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'carlos@vet.com',
            'password' => 'senhaerrada'
        ]);

        // Deve retornar 401 (Não Autorizado)
        $response->assertStatus(401);
    }

    public function test_nao_eh_possivel_acessar_tutores_sem_estar_logado()
    {
        // Tenta listar tutores SEM enviar o token
        $response = $this->getJson('/api/tutores');

        // O Laravel deve bloquear e retornar 401 (Não Autorizado)
        $response->assertStatus(401);
    }
}