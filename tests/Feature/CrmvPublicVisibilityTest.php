<?php

namespace Tests\Feature;

use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmvPublicVisibilityTest extends TestCase
{
    use RefreshDatabase;

    // TESTE 1 - CRMV simulado pode aparecer durante os testes
    public function test_crmv_simulado_pode_aparecer_em_testing(): void
    {
        $this->criarVeterinarioSimulado();

        $response = $this->getJson(
            '/api/veterinarios/buscar?cidade=Ibirite&uf=MG'
        );

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    // TESTE 2 - CRMV simulado não pode aparecer em produção
    public function test_crmv_simulado_nao_aparece_em_producao(): void
    {
        $this->criarVeterinarioSimulado();

        // Guardamos o ambiente original do Laravel.
        $ambienteOriginal = app()->environment();

        try {
            // Simulamos somente a identificação do ambiente.
            // A conexão continua sendo vetflow_testing.
            app()->instance('env', 'production');

            $response = $this->getJson(
                '/api/veterinarios/buscar?cidade=Ibirite&uf=MG'
            );

            $response->assertOk();
            $response->assertJsonCount(0, 'data');
        } finally {
            // Restauramos o ambiente original após o teste.
            app()->instance('env', $ambienteOriginal);
        }
    }

    // Cria um veterinário fictício aprovado por simulação.
    private function criarVeterinarioSimulado(): void
    {
        $veterinario = Veterinario::create([
            'nome' => 'Veterinario Ficticio Teste',
            'email' => 'vet.visibilidade@example.test',
            'password' => 'SenhaForte123!',
            'crmv' => 'TESTE-MG-0002',
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);

        // Esses campos são definidos diretamente no teste.
        $veterinario->status_crmv =
            Veterinario::CRMV_APROVADO;

        $veterinario->metodo_verificacao = 'simulado';
        $veterinario->crmv_uf = 'MG';

        $veterinario->save();
    }
}
