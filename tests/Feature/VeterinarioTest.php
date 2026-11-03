<?php

namespace Tests\Feature;

use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VeterinarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_cadastrar_um_veterinario()
    {
        // 1. Cadastrar veterinário pela rota pública
        $response = $this->postJson('/api/register', [
            'nome' => 'Dr. Carlos Mendes',
            'email' => 'carlos@vetflow.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'crmv' => 'CRMV-SP 12345',
            'telefone' => '(11) 99999-9999',
            'especialidade' => 'Clínica Geral',
            'cidade' => 'Ibirité',
            'uf' => 'MG'
        ]);

        // 2. Verificar se o cadastro foi realizado
        $response->assertStatus(201);

        // 3. Verificar se retornou token e veterinário
        $response->assertJsonStructure([
            'token',
            'veterinario'
        ]);

        // 4. Verificar se os dados foram salvos
        $this->assertDatabaseHas('veterinarios', [
            'email' => 'carlos@vetflow.com',
            'crmv' => 'CRMV-SP 12345',
            'cidade' => 'Ibirité',
            'uf' => 'MG'
        ]);

        // 5. Verificar se a senha foi armazenada com hash
        $vet = Veterinario::where(
            'email',
            'carlos@vetflow.com'
        )->firstOrFail();

        $this->assertTrue(
            Hash::check('senha123', $vet->password)
        );
    }

    public function test_nao_deve_permitir_crmv_duplicado()
    {
        // 1. Cadastrar primeiro veterinário
        $primeiraResposta = $this->postJson('/api/register', [
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vetflow.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'crmv' => 'CRMV-SP 12345',
            'telefone' => '(11) 99999-9999',
            'especialidade' => 'Clínica Geral',
            'cidade' => 'Ibirité',
            'uf' => 'MG'
        ]);

        // 2. Confirmar primeiro cadastro
        $primeiraResposta->assertStatus(201);

        // 3. Tentar outro cadastro com o mesmo CRMV
        $response = $this->postJson('/api/register', [
            'nome' => 'Dra. Ana',
            'email' => 'ana@vetflow.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'crmv' => 'CRMV-SP 12345',
            'telefone' => '(31) 99999-9999',
            'especialidade' => 'Clínica Geral',
            'cidade' => 'Belo Horizonte',
            'uf' => 'MG'
        ]);

        // 4. Verificar se o CRMV duplicado foi recusado
        $response->assertStatus(422);

        // 5. Verificar erro no campo CRMV
        $response->assertJsonValidationErrors([
            'crmv'
        ]);

        // 6. Verificar se apenas um CRMV foi cadastrado
        $this->assertEquals(
            1,
            Veterinario::where(
                'crmv',
                'CRMV-SP 12345'
            )->count()
        );
    }
}
