<?php

namespace Tests\Feature;

use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmvVerificationTest extends TestCase
{
    use RefreshDatabase;

    // TESTE 1 - Veterinário novo começa pendente
    public function test_novo_veterinario_comeca_com_crmv_pendente(): void
    {
        $veterinario = $this->criarVeterinario();

        $this->assertEquals(
            Veterinario::CRMV_PENDENTE,
            $veterinario->fresh()->status_crmv
        );
    }

    // TESTE 2 - Campos administrativos não são preenchíveis
    public function test_campos_administrativos_nao_sao_preenchiveis(): void
    {
        $veterinario = new Veterinario();

        $this->assertFalse(
            $veterinario->isFillable('status_crmv')
        );

        $this->assertFalse(
            $veterinario->isFillable('crmv_verificado_por')
        );

        $this->assertFalse(
            $veterinario->isFillable('metodo_verificacao')
        );

        $this->assertFalse(
            $veterinario->isFillable('ativo')
        );
    }

    // TESTE 3 - CRMV pendente não aparece na busca pública
    public function test_busca_publica_nao_exibe_veterinario_pendente(): void
    {
        $this->criarVeterinario();

        $response = $this->getJson(
            '/api/veterinarios/buscar?cidade=Ibirite&uf=MG'
        );

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    // TESTE 4 - CRMV aprovado aparece na busca pública
    public function test_busca_publica_exibe_veterinario_aprovado(): void
    {
        $veterinario = $this->criarVeterinario();

        // Simula a aprovação no banco de testes.
        $veterinario->status_crmv = Veterinario::CRMV_APROVADO;
        $veterinario->save();

        $response = $this->getJson(
            '/api/veterinarios/buscar?cidade=Ibirite&uf=MG'
        );

        $response->assertOk();
        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.id',
            $veterinario->id
        );
    }

    // TESTE 5 - CRMV rejeitado não aparece na busca pública
    public function test_busca_publica_nao_exibe_veterinario_rejeitado(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv = Veterinario::CRMV_REJEITADO;
        $veterinario->save();

        $response = $this->getJson(
            '/api/veterinarios/buscar?cidade=Ibirite&uf=MG'
        );

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    // TESTE 6 - Veterinário inativo não aparece na busca pública
    public function test_busca_publica_nao_exibe_veterinario_inativo(): void
    {
        $veterinario = $this->criarVeterinario();

        $veterinario->status_crmv = Veterinario::CRMV_APROVADO;
        $veterinario->ativo = false;
        $veterinario->save();

        $response = $this->getJson(
            '/api/veterinarios/buscar?cidade=Ibirite&uf=MG'
        );

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    // Função auxiliar para criar veterinário fictício
    private function criarVeterinario(): Veterinario
    {
        return Veterinario::create([
            'nome' => 'Veterinaria Exemplo Teste',
            'email' => 'vet.crmv.teste@example.com',
            'password' => 'SenhaForte123!',
            'crmv' => 'TESTE-MG-0001',
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);
    }
}
