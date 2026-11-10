<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrmvQueueTest extends TestCase
{
    use RefreshDatabase;

    // TESTE 1 - Admin vê apenas os CRMVs pendentes
    public function test_admin_visualiza_somente_crmvs_pendentes(): void
    {
        $admin = $this->criarAdmin();

        $pendente = $this->criarVeterinario(
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
            ->getJson('/admin/crmv/pendentes');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.id',
            $pendente->id
        );

        $response->assertJsonPath(
            'meta.total',
            1
        );
    }

    // TESTE 2 - API não expõe informações desnecessárias
    public function test_fila_nao_expoe_dados_desnecessarios(): void
    {
        $admin = $this->criarAdmin();

        $this->criarVeterinario(
            1,
            Veterinario::CRMV_PENDENTE
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/crmv/pendentes');

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'nome',
                    'crmv',
                    'crmv_uf',
                    'cidade',
                    'uf',
                    'especialidade',
                    'ativo',
                    'created_at',
                ],
            ],
            'meta' => [
                'pagina_atual',
                'ultima_pagina',
                'por_pagina',
                'total',
            ],
        ]);

        $response->assertJsonMissingPath('data.0.email');
        $response->assertJsonMissingPath('data.0.telefone');
        $response->assertJsonMissingPath('data.0.password');
    }

    // TESTE 3 - Investidor não pode acessar a fila
    public function test_investidor_nao_pode_visualizar_fila(): void
    {
        $investidor = $this->criarAdmin(
            User::PAPEL_INVESTIDOR
        );

        $response = $this
            ->actingAs($investidor, 'admin')
            ->getJson('/admin/crmv/pendentes');

        $response->assertForbidden();
    }

    // TESTE 4 - Visitante não pode acessar
    public function test_visitante_nao_pode_visualizar_fila(): void
    {
        $response = $this->getJson(
            '/admin/crmv/pendentes'
        );

        $response->assertForbidden();
    }

    // TESTE 5 - Administrador inativo não pode acessar
    public function test_admin_inativo_nao_pode_visualizar_fila(): void
    {
        $admin = $this->criarAdmin(
            User::PAPEL_ADMIN,
            false
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/crmv/pendentes');

        $response->assertForbidden();
    }

    // TESTE 6 - Usuário sem papel não pode acessar
    public function test_usuario_sem_papel_nao_pode_visualizar_fila(): void
    {
        $usuario = $this->criarAdmin('');

        $response = $this
            ->actingAs($usuario, 'admin')
            ->getJson('/admin/crmv/pendentes');

        $response->assertForbidden();
    }

    // TESTE 7 - Veterinário comum não pode acessar
    public function test_veterinario_nao_pode_visualizar_fila(): void
    {
        $veterinario = $this->criarVeterinario(
            1,
            Veterinario::CRMV_APROVADO
        );

        $response = $this
            ->actingAs($veterinario, 'web')
            ->getJson('/admin/crmv/pendentes');

        $response->assertForbidden();
    }

    // TESTE 8 - Paginação com mais de 10 registros
    public function test_fila_possui_paginacao(): void
    {
        $admin = $this->criarAdmin();

        for ($numero = 1; $numero <= 12; $numero++) {
            $this->criarVeterinario(
                $numero,
                Veterinario::CRMV_PENDENTE
            );
        }

        $primeiraPagina = $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/crmv/pendentes');

        $primeiraPagina->assertOk();
        $primeiraPagina->assertJsonCount(10, 'data');
        $primeiraPagina->assertJsonPath('meta.total', 12);
        $primeiraPagina->assertJsonPath('meta.pagina_atual', 1);
        $primeiraPagina->assertJsonPath('meta.ultima_pagina', 2);

        $segundaPagina = $this->getJson(
            '/admin/crmv/pendentes?page=2'
        );

        $segundaPagina->assertOk();
        $segundaPagina->assertJsonCount(2, 'data');
        $segundaPagina->assertJsonPath('meta.pagina_atual', 2);
        $segundaPagina->assertJsonPath('meta.total', 12);
    }

    // Função auxiliar para criar usuários administrativos
    private function criarAdmin(
        string $papel = User::PAPEL_ADMIN,
        bool $ativo = true
    ): User {
        $usuario = User::factory()->create();

        $usuario->papel = $papel;
        $usuario->ativo = $ativo;
        $usuario->save();

        return $usuario;
    }

    // Função auxiliar para criar veterinários de teste
    private function criarVeterinario(
        int $numero,
        string $status
    ): Veterinario {
        $veterinario = Veterinario::create([
            'nome' => "Veterinario Teste {$numero}",
            'email' => "vet.fila{$numero}@example.test",
            'password' => 'SenhaForte123!',
            'crmv' => "TESTE-FILA-{$numero}",
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);

        // Simulação realizada apenas no banco de testes.
        $veterinario->status_crmv = $status;
        $veterinario->save();

        return $veterinario;
    }
}
