<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VeterinarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_cadastrar_um_veterinario()
    {
        $response = $this->postJson('/api/veterinarios', [
            'nome' => 'Dr. Carlos Mendes',
            'email' => 'carlos@vetflow.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'crmv' => 'CRMV-SP 12345',
            'telefone' => '(11) 99999-9999',
            'especialidade' => 'Clínica Geral'
        ]);

        // Deve retornar 201 (Criado)
        $response->assertStatus(201);
        
        // Verifica se salvou no banco (e se a senha está criptografada)
        $this->assertDatabaseHas('veterinarios', [
            'email' => 'carlos@vetflow.com',
            'crmv' => 'CRMV-SP 12345'
        ]);
    }

    public function test_nao_deve_permitir_crmv_duplicado()
    {
        // Cria o primeiro
        $this->postJson('/api/veterinarios', [
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vetflow.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'crmv' => 'CRMV-SP 12345',
        ]);

        // Tenta criar o segundo com o mesmo CRMV
        $response = $this->postJson('/api/veterinarios', [
            'nome' => 'Dra. Ana',
            'email' => 'ana@vetflow.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'crmv' => 'CRMV-SP 12345', // Duplicado!
        ]);

        // Deve falhar com 422
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['crmv']);
    }
}