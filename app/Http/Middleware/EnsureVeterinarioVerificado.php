<?php

namespace App\Http\Middleware;

use App\Models\Veterinario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVeterinarioVerificado
{
    /**
     * Verifica se o veterinário está autorizado
     * a utilizar as funcionalidades profissionais.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // 1. Identifica o usuário autenticado.
        $veterinario = $request->user();

        // 2. Confirma que é um veterinário.
        if (! ($veterinario instanceof Veterinario)) {
            return response()->json([
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        // 3. Confirma que a conta está ativa.
        if (! $veterinario->ativo) {
            return response()->json([
                'message' => 'Sua conta está inativa.',
            ], 403);
        }

        // 4. Confirma que o CRMV está aprovado.
        if (
            $veterinario->status_crmv !==
            Veterinario::CRMV_APROVADO
        ) {
            return response()->json([
                'message' =>
                    'Seu CRMV precisa ser aprovado para acessar esta funcionalidade.',
            ], 403);
        }

        // 5. Permite CRMV aprovado manualmente.
        if (
            $veterinario->metodo_verificacao === 'manual'
        ) {
            return $next($request);
        }

        // 6. Permite CRMV simulado somente em
        // ambiente local ou de testes.
        if (
            $veterinario->metodo_verificacao === 'simulado' &&
            app()->environment('local', 'testing')
        ) {
            return $next($request);
        }

        // 7. Bloqueia qualquer outro caso.
        return response()->json([
            'message' =>
                'A verificação do CRMV não permite acesso profissional neste ambiente.',
        ], 403);
    }
}
