<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserAdminPermissionTest extends TestCase
{
    private function criarUsuario(
        ?string $papel,
        bool $ativo
    ): User {
        $usuario = new User();

        $usuario->papel = $papel;
        $usuario->ativo = $ativo;

        return $usuario;
    }

    public function test_admin_ativo_possui_permissoes(): void
    {
        $usuario = $this->criarUsuario('admin', true);

        $this->assertTrue(
            $usuario->podeAcessarAdministracao()
        );

        $this->assertTrue(
            $usuario->podeAprovarCrmv()
        );
    }

    public function test_investidor_nao_pode_aprovar_crmv(): void
    {
        $usuario = $this->criarUsuario(
            'investidor',
            true
        );

        $this->assertTrue(
            $usuario->podeAcessarAdministracao()
        );

        $this->assertFalse(
            $usuario->podeAprovarCrmv()
        );
    }

    public function test_conta_desativada_nao_tem_acesso(): void
    {
        foreach (['admin', 'investidor'] as $papel) {
            $usuario = $this->criarUsuario(
                $papel,
                false
            );

            $this->assertFalse(
                $usuario->podeAcessarAdministracao()
            );

            $this->assertFalse(
                $usuario->podeAprovarCrmv()
            );
        }
    }

    public function test_papel_invalido_nao_tem_acesso(): void
    {
        foreach ([null, 'veterinario'] as $papel) {
            $usuario = $this->criarUsuario(
                $papel,
                true
            );

            $this->assertFalse(
                $usuario->podeAcessarAdministracao()
            );

            $this->assertFalse(
                $usuario->podeAprovarCrmv()
            );
        }
    }
}
