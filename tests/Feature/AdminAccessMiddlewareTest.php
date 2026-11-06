<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminAccessMiddlewareTest extends TestCase
{
    private function criarUsuario(
        string $papel,
        bool $ativo
    ): User {
        $usuario = new User();

        $usuario->papel = $papel;
        $usuario->ativo = $ativo;

        return $usuario;
    }

    private function executarMiddleware(
        ?User $usuario,
        string $permissao = 'acessar'
    ) {
        $request = Request::create('/admin/teste', 'GET');

        $request->setUserResolver(
            fn (?string $guard = null) =>
                $guard === 'admin' ? $usuario : null
        );

        $middleware = new EnsureAdminAccess();

        return $middleware->handle(
            $request,
            fn ($request) => response()->json(['ok' => true]),
            $permissao
        );
    }

    public function test_admin_ativo_pode_acessar(): void
    {
        $admin = $this->criarUsuario('admin', true);

        $resposta = $this->executarMiddleware($admin);

        $this->assertSame(200, $resposta->getStatusCode());
    }

    public function test_admin_ativo_pode_aprovar_crmv(): void
    {
        $admin = $this->criarUsuario('admin', true);

        $resposta = $this->executarMiddleware(
            $admin,
            'aprovar-crmv'
        );

        $this->assertSame(200, $resposta->getStatusCode());
    }

    public function test_investidor_pode_acessar_painel(): void
    {
        $investidor = $this->criarUsuario('investidor', true);

        $resposta = $this->executarMiddleware($investidor);

        $this->assertSame(200, $resposta->getStatusCode());
    }

    public function test_investidor_nao_pode_aprovar_crmv(): void
    {
        $investidor = $this->criarUsuario('investidor', true);

        $this->expectException(HttpException::class);

        $this->executarMiddleware($investidor, 'aprovar-crmv');
    }

    public function test_conta_desativada_nao_pode_acessar(): void
    {
        $admin = $this->criarUsuario('admin', false);

        $this->expectException(HttpException::class);

        $this->executarMiddleware($admin);
    }

    public function test_visitante_nao_pode_acessar(): void
    {
        $this->expectException(HttpException::class);

        $this->executarMiddleware(null);
    }

    public function test_permissao_desconhecida_e_bloqueada(): void
    {
        $admin = $this->criarUsuario('admin', true);

        $this->expectException(HttpException::class);

        $this->executarMiddleware($admin, 'permissao-inexistente');
    }
}
