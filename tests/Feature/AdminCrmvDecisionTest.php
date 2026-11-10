<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrmvDecisionTest extends TestCase
{
    use RefreshDatabase;

    // TESTE 1 - Aprovação após consulta manual
    public function test_admin_aprova_crmv_com_conferencia_manual(): void
    {
        $admin = $this->criarAdmin();
        $veterinario = $this->criarVeterinario();

        $response = $this
            ->actingAs($admin, 'admin')
            ->patchJson(
                "/admin/crmv/{$veterinario->id}/aprovar",
                [
                    'metodo_verificacao' => 'manual',
                    'crmv_uf' => 'MG',
                    'fonte_verificacao' => 'CFMV/SISCAD',
                    'consulta_confirmada' => true,
                ]
            );

        $response->assertOk();

        $this->assertDatabaseHas('veterinarios', [
            'id' => $veterinario->id,
            'status_crmv' => Veterinario::CRMV_APROVADO,
            'metodo_verificacao' => 'manual',
            'crmv_uf' => 'MG',
            'fonte_verificacao' => 'CFMV/SISCAD',
            'crmv_verificado_por' => $admin->id,
        ]);

        $this->assertNotNull(
            $veterinario->fresh()->crmv_verificado_em
        );
    }

    // TESTE 2 - Aprovação simulada no ambiente de testes
    public function test_admin_pode_simular_aprovacao_em_ambiente_de_teste(): void
    {
        $admin = $this->criarAdmin();

        $veterinario = $this->criarVeterinario(
            'TESTE-MG-0001'
        );

        $response = $this
            ->actingAs($admin, 'admin')
            ->patchJson(
                "/admin/crmv/{$veterinario->id}/aprovar",
                [
                    'metodo_verificacao' => 'simulado',
                    'crmv_uf' => 'MG',
                ]
            );

        $response->assertOk();

        $this->assertDatabaseHas('veterinarios', [
            'id' => $veterinario->id,
            'status_crmv' => Veterinario::CRMV_APROVADO,
            'metodo_verificacao' => 'simulado',
            'crmv_verificado_por' => $admin->id,
        ]);
    }

    // TESTE 3 - Rejeição exige justificativa
    public function test_admin_rejeita_crmv_com_justificativa(): void
    {
        $admin = $this->criarAdmin();
        $veterinario = $this->criarVeterinario();

        $response = $this
            ->actingAs($admin, 'admin')
            ->patchJson(
                "/admin/crmv/{$veterinario->id}/rejeitar",
                [
                    'motivo_rejeicao_crmv' =>
                        'Registro informado nao corresponde aos dados consultados.',
                ]
            );

        $response->assertOk();

        $this->assertDatabaseHas('veterinarios', [
            'id' => $veterinario->id,
            'status_crmv' => Veterinario::CRMV_REJEITADO,
            'crmv_verificado_por' => $admin->id,
        ]);

        $this->assertNotNull(
            $veterinario->fresh()->motivo_rejeicao_crmv
        );
    }

    // TESTE 4 - Investidor não pode decidir
    public function test_investidor_nao_pode_aprovar_nem_rejeitar(): void
    {
        $investidor = $this->criarAdmin(
            User::PAPEL_INVESTIDOR
        );

        $veterinario = $this->criarVeterinario();

        $this->actingAs($investidor, 'admin');

        $this->patchJson(
            "/admin/crmv/{$veterinario->id}/aprovar",
            [
                'metodo_verificacao' => 'manual',
                'crmv_uf' => 'MG',
                'fonte_verificacao' => 'CFMV/SISCAD',
                'consulta_confirmada' => true,
            ]
        )->assertForbidden();

        $this->patchJson(
            "/admin/crmv/{$veterinario->id}/rejeitar",
            [
                'motivo_rejeicao_crmv' =>
                    'Registro nao confirmado na consulta.',
            ]
        )->assertForbidden();

        $this->assertEquals(
            Veterinario::CRMV_PENDENTE,
            $veterinario->fresh()->status_crmv
        );
    }

    // TESTE 5 - Visitante não pode aprovar
    public function test_visitante_nao_pode_aprovar_crmv(): void
    {
        $veterinario = $this->criarVeterinario();

        $response = $this->patchJson(
            "/admin/crmv/{$veterinario->id}/aprovar",
            [
                'metodo_verificacao' => 'manual',
                'crmv_uf' => 'MG',
                'fonte_verificacao' => 'CFMV/SISCAD',
                'consulta_confirmada' => true,
            ]
        );

        $response->assertForbidden();
    }

    // TESTE 6 - Administrador inativo não pode aprovar
    public function test_admin_inativo_nao_pode_aprovar_crmv(): void
    {
        $admin = $this->criarAdmin(
            User::PAPEL_ADMIN,
            false
        );

        $veterinario = $this->criarVeterinario();

        $response = $this
            ->actingAs($admin, 'admin')
            ->patchJson(
                "/admin/crmv/{$veterinario->id}/aprovar",
                [
                    'metodo_verificacao' => 'manual',
                    'crmv_uf' => 'MG',
                    'fonte_verificacao' => 'CFMV/SISCAD',
                    'consulta_confirmada' => true,
                ]
            );

        $response->assertForbidden();
    }

    // TESTE 7 - Consulta manual precisa ser confirmada
    public function test_aprovacao_manual_exige_confirmacao(): void
    {
        $admin = $this->criarAdmin();
        $veterinario = $this->criarVeterinario();

        $response = $this
            ->actingAs($admin, 'admin')
            ->patchJson(
                "/admin/crmv/{$veterinario->id}/aprovar",
                [
                    'metodo_verificacao' => 'manual',
                    'crmv_uf' => 'MG',
                    'fonte_verificacao' => 'CFMV/SISCAD',
                    'consulta_confirmada' => false,
                ]
            );

        $response->assertUnprocessable();

        $this->assertEquals(
            Veterinario::CRMV_PENDENTE,
            $veterinario->fresh()->status_crmv
        );
    }

    // TESTE 8 - Evitar decisões duplicadas
    public function test_crmv_ja_analisado_nao_pode_ser_aprovado_novamente(): void
    {
        $admin = $this->criarAdmin();
        $veterinario = $this->criarVeterinario();

        $this->actingAs($admin, 'admin');

        $this->patchJson(
            "/admin/crmv/{$veterinario->id}/rejeitar",
            [
                'motivo_rejeicao_crmv' =>
                    'Registro nao confirmado na fonte consultada.',
            ]
        )->assertOk();

        $response = $this->patchJson(
            "/admin/crmv/{$veterinario->id}/aprovar",
            [
                'metodo_verificacao' => 'manual',
                'crmv_uf' => 'MG',
                'fonte_verificacao' => 'CFMV/SISCAD',
                'consulta_confirmada' => true,
            ]
        );

        $response->assertStatus(409);

        $this->assertEquals(
            Veterinario::CRMV_REJEITADO,
            $veterinario->fresh()->status_crmv
        );
    }

    // Auxiliar: cria administrador ou investidor
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

    // Auxiliar: cria veterinário fictício
    private function criarVeterinario(
        string $crmv = '12345'
    ): Veterinario {
        return Veterinario::create([
            'nome' => 'Profissional de Teste',
            'email' => 'vet.decisao@example.test',
            'password' => 'SenhaForte123!',
            'crmv' => $crmv,
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);
    }
}
