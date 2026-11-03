<?php

namespace Tests\Feature;

use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TutorTest extends TestCase
{
    use RefreshDatabase;

    // Executado antes de cada teste
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Criar veterinário para o teste
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        // 2. Autenticar veterinário com Sanctum
        Sanctum::actingAs($vet);
    }

    public function test_pode_criar_um_tutor()
    {
        // 1. Enviar cadastro do tutor
        $response = $this->postJson('/api/tutores', [
            'nome' => 'Maria Silva',
            'cpf' => '123.456.789-00',
            'telefone' => '(11) 99999-9999',
            'email' => 'maria@email.com',
            'endereco' => 'Rua das Flores, 123'
        ]);

        // 2. Verificar se foi criado
        $response->assertStatus(201);
    }

    public function test_nao_deve_permitir_cadastrar_tutor_com_cpf_duplicado()
    {
        // 1. Cadastrar primeiro tutor
        $primeiroResponse = $this->postJson('/api/tutores', [
            'nome' => 'João Silva',
            'cpf' => '111.222.333-44',
            'telefone' => '(11) 88888-8888',
            'email' => 'joao@email.com',
            'endereco' => 'Rua A, 100'
        ]);

        // Confirmar primeiro cadastro
        $primeiroResponse->assertStatus(201);

        // 2. Tentar outro tutor com o mesmo CPF
        $response = $this->postJson('/api/tutores', [
            'nome' => 'Maria Souza',
            'cpf' => '111.222.333-44',
            'telefone' => '(21) 99999-9999',
            'email' => 'maria@email.com',
            'endereco' => 'Rua B, 200'
        ]);

        // 3. Verificar se o sistema recusou
        $response->assertStatus(422);

        // 4. Verificar erro no campo CPF
        $response->assertJsonValidationErrors(['cpf']);
    }
}
