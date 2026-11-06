<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSessionRouteTest extends TestCase
{
    use RefreshDatabase;

    private function criarConta(
        ?string $papel,
        bool $ativo
    ): User {
        $usuario = User::factory()->create();

        // Campos protegidos contra preenchimento em massa
        $usuario->papel = $papel;
        $usuario->ativo = $ativo;
        $usuario->save();

        return $usuario;
    }

    public function test_admin_ativo_acessa_sua_sessao(): void
    {
        $admin = $this->criarConta('admin', true);

        $this->actingAs($admin, 'admin')
            ->getJson('/admin/me')
            ->assertOk()
            ->assertExactJson([
                'id' => $admin->id,
                'nome' => $admin->name,
                'papel' => 'admin',
            ]);
    }

    public function test_investidor_ativo_acessa_sua_sessao(): void
    {
        $investidor = $this->criarConta(
            'investidor',
            true
        );

        $this->actingAs($investidor, 'admin')
            ->getJson('/admin/me')
            ->assertOk()
            ->assertJsonPath('papel', 'investidor');
    }

    public function test_visitante_nao_pode_acessar(): void
    {
        $this->getJson('/admin/me')
            ->assertForbidden();
    }

    public function test_admin_desativado_nao_pode_acessar(): void
    {
        $admin = $this->criarConta('admin', false);

        $this->actingAs($admin, 'admin')
            ->getJson('/admin/me')
            ->assertForbidden();
    }

    public function test_conta_sem_papel_nao_pode_acessar(): void
    {
        $usuario = $this->criarConta(null, true);

        $this->actingAs($usuario, 'admin')
            ->getJson('/admin/me')
            ->assertForbidden();
    }

    public function test_login_veterinario_nao_acessa_admin(): void
    {
        $veterinario = new Veterinario();

        $this->actingAs($veterinario, 'web')
            ->getJson('/admin/me')
            ->assertForbidden();
    }
}
