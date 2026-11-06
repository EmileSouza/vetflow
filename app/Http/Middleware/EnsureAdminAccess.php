<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(
        Request $request,
        Closure $next,
        string $permissao = 'acessar'
    ): Response {
        // Consulta exclusivamente a sessão administrativa.
        $usuario = $request->user('admin');

        // Impede acesso de visitantes, veterinários,
        // contas desativadas ou sem papel permitido.
        if (
            ! $usuario instanceof User ||
            ! $usuario->podeAcessarAdministracao()
        ) {
            abort(403, 'Acesso administrativo não autorizado.');
        }

        // Negamos permissões desconhecidas.
        if (! in_array($permissao, ['acessar', 'aprovar-crmv'], true)) {
            abort(403, 'Permissão administrativa inválida.');
        }

        // Apenas administradoras podem aprovar CRMV.
        if (
            $permissao === 'aprovar-crmv' &&
            ! $usuario->podeAprovarCrmv()
        ) {
            abort(403, 'Você não tem permissão para aprovar CRMV.');
        }

        return $next($request);
    }
}
