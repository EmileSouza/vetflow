<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // 1. Importa a ferramenta de limpeza
use Tests\TestCase;

class TutorTest extends TestCase
{
    use RefreshDatabase; // 2. Ativa a ferramenta de limpeza para esta classe

    public function test_pode_criar_um_tutor()
    {
        $response = $this->postJson('/api/tutores', [
            'nome' => 'Maria Silva',
            'cpf' => '123.456.789-00',
            'telefone' => '(11) 99999-9999',
            'email' => 'maria@email.com',
            'endereco' => 'Rua das Flores, 123'
        ]);

        $response->assertStatus(201);
    }

    public function test_nao_deve_permitir_cadastrar_tutor_com_cpf_duplicado()
    {
        // 1. Primeiro, criamos um tutor válido no banco (que será limpo depois)
        $this->postJson('/api/tutores', [
            'nome' => 'João Silva',
            'cpf' => '111.222.333-44',
            'telefone' => '(11) 88888-8888',
            'email' => 'joao@email.com',
            'endereco' => 'Rua A, 100'
        ]);

        // 2. Agora, tentamos criar OUTRO tutor com o MESMO CPF
        $response = $this->postJson('/api/tutores', [
            'nome' => 'Maria Souza',
            'cpf' => '111.222.333-44',
            'telefone' => '(21) 99999-9999',
            'email' => 'maria@email.com',
            'endereco' => 'Rua B, 200'
        ]);

        // 3. O sistema DEVE recusar com o código 422
        $response->assertStatus(422);

        // 4. E deve devolver uma mensagem de erro específica no campo 'cpf'
        $response->assertJsonValidationErrors(['cpf']);
    }
}