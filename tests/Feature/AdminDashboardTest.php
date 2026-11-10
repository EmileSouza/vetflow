<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    // TESTE 1 - Administrador consulta os indicadores
    public function test_admin_ativo_consulta_indicadores_reais(): void
    {
        $admin = $this->criarUsuarioAdministrativo(
            User::PAPEL_ADMIN
        );

        $this->criarVeterinario(
            1,
            Veterinario::CRMV_PENDENTE
        );

        $this->criarVeterinario(
            2,
            Veterinario::CRMV_APROVADO
        );

        $this->criarVeterinario(
            3,
            Veterinario::CRMV_REJEITADO
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/dashboard/resumo');

        $response->assertOk();

        $response->assertExactJson([
            'data' => [
                'veterinarios_total' => 3,
                'crmv_pendentes' => 1,
                'crmv_aprovados' => 1,
                'crmv_rejeitados' => 1,
                'tutores_total' => 0,
                'pets_total' => 0,
                'agendamentos_total' => 0,
            ],
        ]);
    }

    // TESTE 2 - Investidor pode consultar indicadores
    public function test_investidor_ativo_pode_consultar_indicadores(): void
    {
        $investidor = $this->criarUsuarioAdministrativo(
            User::PAPEL_INVESTIDOR
        );

        $response = $this
            ->actingAs($investidor, 'admin')
            ->getJson('/admin/dashboard/resumo');

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'veterinarios_total',
                'crmv_pendentes',
                'crmv_aprovados',
                'crmv_rejeitados',
                'tutores_total',
                'pets_total',
                'agendamentos_total',
            ],
        ]);
    }

    // TESTE 3 - Visitante sem login não pode acessar
    public function test_visitante_nao_pode_consultar_dashboard(): void
    {
        $response = $this->getJson(
            '/admin/dashboard/resumo'
        );

        $response->assertForbidden();
    }

    // TESTE 4 - Administrador inativo não pode acessar
    public function test_admin_inativo_nao_pode_consultar_dashboard(): void
    {
        $admin = $this->criarUsuarioAdministrativo(
            User::PAPEL_ADMIN,
            false
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/dashboard/resumo');

        $response->assertForbidden();
    }

    // TESTE 5 - Usuário sem papel administrativo não acessa
    public function test_usuario_sem_papel_nao_pode_consultar_dashboard(): void
    {
        $usuario = $this->criarUsuarioAdministrativo(
            ''
        );

        $response = $this
            ->actingAs($usuario, 'admin')
            ->getJson('/admin/dashboard/resumo');

        $response->assertForbidden();
    }

    // TESTE 6 - Veterinário comum não pode acessar
    public function test_veterinario_nao_pode_consultar_dashboard(): void
    {
        $veterinario = $this->criarVeterinario(
            1,
            Veterinario::CRMV_APROVADO
        );

        $response = $this
            ->actingAs($veterinario, 'web')
            ->getJson('/admin/dashboard/resumo');

        $response->assertForbidden();
    }

    // Função auxiliar para criar usuários de teste
    private function criarUsuarioAdministrativo(
        string $papel,
        bool $ativo = true
    ): User {
        $usuario = User::factory()->create();

        $usuario->papel = $papel;
        $usuario->ativo = $ativo;
        $usuario->save();

        return $usuario;
    }

    // Função auxiliar para criar veterinários fictícios
    private function criarVeterinario(
        int $numero,
        string $status
    ): Veterinario {
        $veterinario = Veterinario::create([
            'nome' => "Veterinario Teste {$numero}",
            'email' => "vet{$numero}@example.test",
            'password' => 'SenhaForte123!',
            'crmv' => "TESTE-{$numero}",
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);

        // Altera o status somente no banco de testes.
        $veterinario->status_crmv = $status;
        $veterinario->save();

        return $veterinario;
    }
}
