<?php

namespace Tests\Feature;

use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmvProfessionalAccessTest extends TestCase
{
    use RefreshDatabase;

    // TESTE 1 - Pendente não acessa recursos profissionais
    public function test_veterinario_pendente_nao_acessa_recursos_profissionais(): void
    {
        $veterinario = $this->criarVeterinario();

        $this->autenticarVeterinario($veterinario);

        $this->getJson('/api/tutores')
            ->assertForbidden();

        $this->getJson('/api/pets')
            ->assertForbidden();

        $this->getJson('/api/agendamentos')
            ->assertForbidden();

        $this->getJson(
            '/api/veterinarios?cidade=Ibirite&uf=MG'
        )->assertForbidden();
    }

    // TESTE 2 - CRMV rejeitado não acessa
    public function test_veterinario_rejeitado_nao_acessa_recursos_profissionais(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv =
            Veterinario::CRMV_REJEITADO;

        $veterinario->save();

        $this->autenticarVeterinario($veterinario);

        $this->getJson('/api/tutores')
            ->assertForbidden();
    }

    // TESTE 3 - Veterinário inativo não acessa
    public function test_veterinario_inativo_nao_acessa_recursos_profissionais(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv =
            Veterinario::CRMV_APROVADO;

        $veterinario->metodo_verificacao = 'manual';
        $veterinario->ativo = false;

        $veterinario->save();

        $this->autenticarVeterinario($veterinario);

        $this->getJson('/api/tutores')
            ->assertForbidden();
    }

    // TESTE 4 - Aprovado manualmente pode acessar
    public function test_veterinario_aprovado_manualmente_pode_acessar(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv =
            Veterinario::CRMV_APROVADO;

        $veterinario->metodo_verificacao = 'manual';
        $veterinario->ativo = true;

        $veterinario->save();

        $this->autenticarVeterinario($veterinario);

        $this->getJson('/api/tutores')
            ->assertOk();
    }

    // TESTE 5 - Simulação permitida em ambiente de testes
    public function test_veterinario_simulado_pode_acessar_em_testing(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv =
            Veterinario::CRMV_APROVADO;

        $veterinario->metodo_verificacao = 'simulado';
        $veterinario->ativo = true;

        $veterinario->save();

        $this->autenticarVeterinario($veterinario);

        $this->getJson('/api/tutores')
            ->assertOk();
    }

    // TESTE 6 - Simulado não pode atuar em produção
    public function test_veterinario_simulado_nao_pode_atuar_em_producao(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv =
            Veterinario::CRMV_APROVADO;

        $veterinario->metodo_verificacao = 'simulado';
        $veterinario->ativo = true;

        $veterinario->save();

        $this->autenticarVeterinario($veterinario);

        $ambienteOriginal = app()->environment();

        try {
            // Apenas simula o ambiente.
            // O banco continua sendo vetflow_testing.
            app()->instance('env', 'production');

            $this->getJson('/api/tutores')
                ->assertForbidden();
        } finally {
            app()->instance('env', $ambienteOriginal);
        }
    }

    // TESTE 7 - Pendente ainda pode fazer login
    public function test_veterinario_pendente_pode_fazer_login(): void
    {
        $veterinario = $this->criarVeterinario();

        $response = $this->postJson('/api/login', [
            'email' => $veterinario->email,
            'password' => 'SenhaForte123!',
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'token',
            'veterinario',
        ]);
    }

    // TESTE 8 - Pendente pode encerrar a sessão
    public function test_veterinario_pendente_pode_fazer_logout(): void
    {
        $veterinario = $this->criarVeterinario();

        $this->autenticarVeterinario($veterinario);

        $this->postJson('/api/logout')
            ->assertOk();
    }

    // Cria um veterinário fictício no banco de testes.
    private function criarVeterinario(): Veterinario
    {
        return Veterinario::create([
            'nome' => 'Veterinario Teste Acesso',
            'email' => 'vet.acesso@example.test',
            'password' => 'SenhaForte123!',
            'crmv' => 'TESTE-ACESSO-001',
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);
    }

    // Gera um token real para testar o Sanctum.
    private function autenticarVeterinario(
        Veterinario $veterinario
    ): void {
        $token = $veterinario
            ->createToken('teste-acesso-crmv')
            ->plainTextToken;

        $this->withToken($token);
    }
}
