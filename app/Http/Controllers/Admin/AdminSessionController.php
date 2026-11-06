<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AdminSessionController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Limita tentativas incorretas por e-mail e IP.
        $chave = 'admin-login:' . hash(
            'sha256',
            strtolower($dados['email']) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            return response()->json([
                'message' => 'Muitas tentativas. Tente novamente mais tarde.',
            ], 429);
        }

        // Busca exclusivamente na tabela users.
        $usuario = User::where('email', $dados['email'])->first();

        $senhaCorreta = $usuario
            && Hash::check($dados['password'], $usuario->password);

        // Não permite contas inativas ou sem papel válido.
        if (
            ! $senhaCorreta ||
            ! $usuario->podeAcessarAdministracao()
        ) {
            RateLimiter::hit($chave, 60);

            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], 401);
        }

        RateLimiter::clear($chave);

        // Login exclusivo no guard administrativo.
        Auth::guard('admin')->login($usuario);

        // Previne fixação de sessão.
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login administrativo realizado com sucesso!',
            'usuario' => [
                'id' => $usuario->id,
                'nome' => $usuario->name,
                'papel' => $usuario->papel,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout administrativo realizado com sucesso!',
        ]);
    }
}
