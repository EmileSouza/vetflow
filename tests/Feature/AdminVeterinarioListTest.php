<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVeterinarioListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_lista_veterinarios_de_todos_os_status(): void
    {
        $admin = $this->criarAdmin();

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

        $resposta = $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios');

        $resposta
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nome',
                        'crmv',
                        'status_crmv',
                        'uf',
                    ],
                ],
                'meta' => [
                    'pagina_atual',
                    'ultima_pagina',
                    'por_pagina',
                    'total',
                ],
            ]);
    }

    public function test_admin_filtra_por_status(): void
    {
        $admin = $this->criarAdmin();

        $aprovado = $this->criarVeterinario(
            1,
            Veterinario::CRMV_APROVADO
        );

        $this->criarVeterinario(
            2,
            Veterinario::CRMV_PENDENTE
        );

        $this->criarVeterinario(
            3,
            Veterinario::CRMV_REJEITADO
        );

        $this
            ->actingAs($admin, 'admin')
            ->getJson(
                '/admin/veterinarios?status_crmv=aprovado'
            )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $aprovado->id
            );
    }

    public function test_admin_pesquisa_por_nome_e_crmv(): void
    {
        $admin = $this->criarAdmin();

        $veterinario = $this->criarVeterinario(
            1,
            Veterinario::CRMV_PENDENTE
        );

        $this->criarVeterinario(
            2,
            Veterinario::CRMV_PENDENTE
        );

        $veterinario->nome = 'Rafaela Souza';
        $veterinario->save();

        // Pesquisa pelo nome.
        $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios?busca=Rafaela')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $veterinario->id
            );

        // Pesquisa pelo CRMV.
        $this
            ->getJson(
                '/admin/veterinarios?busca=TESTE-GESTAO-1'
            )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $veterinario->id
            );
    }

    public function test_admin_filtra_por_uf_da_localizacao(): void
    {
        $admin = $this->criarAdmin();

        $mineiro = $this->criarVeterinario(
            1,
            Veterinario::CRMV_PENDENTE,
            'MG'
        );

        $this->criarVeterinario(
            2,
            Veterinario::CRMV_APROVADO,
            'SP'
        );

        $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios?uf=mg')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $mineiro->id
            );
    }

    public function test_lista_tem_paginacao_de_dez_registros(): void
    {
        $admin = $this->criarAdmin();

        for ($i = 1; $i <= 12; $i++) {
            $this->criarVeterinario(
                $i,
                Veterinario::CRMV_PENDENTE
            );
        }

        $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 12)
            ->assertJsonPath('meta.ultima_pagina', 2);

        $this
            ->getJson('/admin/veterinarios?page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.pagina_atual', 2);
    }

    public function test_admin_consulta_detalhes_e_auditoria(): void
    {
        $admin = $this->criarAdmin();

        $veterinario = $this->criarVeterinario(
            1,
            Veterinario::CRMV_REJEITADO
        );

        $veterinario->metodo_verificacao = 'manual';
        $veterinario->fonte_verificacao = 'Consulta CRMV-MG';
        $veterinario->crmv_verificado_em = now();
        $veterinario->crmv_verificado_por = $admin->id;

        $veterinario->motivo_rejeicao_crmv =
            'Registro não localizado no conselho.';

        $veterinario->save();

        $this
            ->actingAs($admin, 'admin')
            ->getJson(
                "/admin/veterinarios/{$veterinario->id}"
            )
            ->assertOk()
            ->assertJsonPath(
                'data.status_crmv',
                'rejeitado'
            )
            ->assertJsonPath(
                'data.verificador_nome',
                $admin->name
            )
            ->assertJsonPath(
                'data.motivo_rejeicao_crmv',
                'Registro não localizado no conselho.'
            )
            ->assertJsonPath(
                'data.fonte_verificacao',
                'Consulta CRMV-MG'
            )
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.email')
            ->assertJsonMissingPath('data.telefone');
    }

    public function test_lista_nao_expoe_dados_sensiveis(): void
    {
        $admin = $this->criarAdmin();

        $this->criarVeterinario(
            1,
            Veterinario::CRMV_PENDENTE
        );

        $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios')
            ->assertOk()
            ->assertJsonMissingPath('data.0.email')
            ->assertJsonMissingPath('data.0.telefone')
            ->assertJsonMissingPath('data.0.password');
    }

    public function test_detalhe_de_id_inexistente_retorna_404(): void
    {
        $admin = $this->criarAdmin();

        $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios/999999')
            ->assertNotFound();
    }

    public function test_filtros_invalidos_retornam_422(): void
    {
        $admin = $this->criarAdmin();

        $this
            ->actingAs($admin, 'admin')
            ->getJson(
                '/admin/veterinarios?status_crmv=desconhecido&uf=ABC'
            )
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'status_crmv',
                'uf',
            ]);
    }

    public function test_visitante_nao_acessa_lista_nem_detalhes(): void
    {
        $this
            ->getJson('/admin/veterinarios')
            ->assertForbidden();

        $this
            ->getJson('/admin/veterinarios/1')
            ->assertForbidden();
    }

    public function test_investidor_nao_acessa_dados_individuais(): void
    {
        $investidor = $this->criarAdmin(
            User::PAPEL_INVESTIDOR
        );

        $this
            ->actingAs($investidor, 'admin')
            ->getJson('/admin/veterinarios')
            ->assertForbidden();

        $this
            ->getJson('/admin/veterinarios/1')
            ->assertForbidden();
    }

    public function test_admin_inativo_nao_acessa_lista(): void
    {
        $admin = $this->criarAdmin(
            User::PAPEL_ADMIN,
            false
        );

        $this
            ->actingAs($admin, 'admin')
            ->getJson('/admin/veterinarios')
            ->assertForbidden();
    }

    public function test_veterinario_comum_nao_acessa_lista(): void
    {
        $veterinario = $this->criarVeterinario(
            1,
            Veterinario::CRMV_APROVADO
        );

        $this
            ->actingAs($veterinario, 'web')
            ->getJson('/admin/veterinarios')
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos auxiliares
    |--------------------------------------------------------------------------
    */

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

    private function criarVeterinario(
        int $numero,
        string $status,
        string $uf = 'MG'
    ): Veterinario {
        $veterinario = Veterinario::create([
            'nome' => "Veterinario Teste {$numero}",
            'email' => "vet.gestao{$numero}@example.test",
            'password' => 'SenhaForte123!',
            'crmv' => "TESTE-GESTAO-{$numero}",
            'cidade' => $uf === 'MG'
                ? 'Ibirite'
                : 'Sao Paulo',
            'uf' => $uf,
        ]);

        // Apenas no banco isolado de testes.
        $veterinario->status_crmv = $status;
        $veterinario->save();

        return $veterinario;
    }
}
