<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    private const SENHA = 'SenhaSegura@2026';

    private function criarConta(
        ?string $papel,
        bool $ativo
    ): User {
        $usuario = User::factory()->create([
            'password' => Hash::make(self::SENHA),
        ]);

        $usuario->papel = $papel;
        $usuario->ativo = $ativo;
        $usuario->save();

        return $usuario;
    }

    public function test_admin_ativo_consegue_login(): void
    {
        $admin = $this->criarConta('admin', true);

        $this->postJson('/admin/session', [
            'email' => $admin->email,
            'password' => self::SENHA,
        ])
            ->assertOk()
            ->assertJsonPath('usuario.papel', 'admin');

        $this->getJson('/admin/me')
            ->assertOk()
            ->assertJsonPath('id', $admin->id);
    }

    public function test_investidor_ativo_consegue_login(): void
    {
        $investidor = $this->criarConta('investidor', true);

        $this->postJson('/admin/session', [
            'email' => $investidor->email,
            'password' => self::SENHA,
        ])
            ->assertOk()
            ->assertJsonPath('usuario.papel', 'investidor');
    }

    public function test_senha_incorreta_e_rejeitada(): void
    {
        $admin = $this->criarConta('admin', true);

        $this->postJson('/admin/session', [
            'email' => $admin->email,
            'password' => 'senha-errada',
        ])->assertUnauthorized();
    }

    public function test_conta_desativada_nao_consegue_login(): void
    {
        $admin = $this->criarConta('admin', false);

        $this->postJson('/admin/session', [
            'email' => $admin->email,
            'password' => self::SENHA,
        ])->assertUnauthorized();
    }

    public function test_conta_sem_papel_nao_consegue_login(): void
    {
        $usuario = $this->criarConta(null, true);

        $this->postJson('/admin/session', [
            'email' => $usuario->email,
            'password' => self::SENHA,
        ])->assertUnauthorized();
    }

    public function test_veterinario_nao_consegue_login_admin(): void
    {
        $veterinario = Veterinario::create([
            'nome' => 'Veterinário Teste',
            'email' => 'vet.admin.teste@example.com',
            'password' => self::SENHA,
            'crmv' => 'CRMV-TESTE-ADMIN',
            'cidade' => 'Ibirité',
            'uf' => 'MG',
            'ativo' => true,
        ]);

        $this->postJson('/admin/session', [
            'email' => $veterinario->email,
            'password' => self::SENHA,
        ])->assertUnauthorized();
    }

    public function test_logout_encerra_sessao_administrativa(): void
    {
        $admin = $this->criarConta('admin', true);

        $this->postJson('/admin/session', [
            'email' => $admin->email,
            'password' => self::SENHA,
        ])->assertOk();

        $this->deleteJson('/admin/session')
            ->assertOk();

        $this->getJson('/admin/me')
            ->assertForbidden();
    }

    public function test_muitas_tentativas_sao_bloqueadas(): void
    {
        $admin = $this->criarConta('admin', true);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/admin/session', [
                'email' => $admin->email,
                'password' => 'senha-incorreta',
            ])->assertUnauthorized();
        }

        $this->postJson('/admin/session', [
            'email' => $admin->email,
            'password' => 'senha-incorreta',
        ])->assertStatus(429);
    }
}
