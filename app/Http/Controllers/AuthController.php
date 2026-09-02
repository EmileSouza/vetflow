<?php

namespace App\Http\Controllers;

use App\Models\Veterinario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $veterinario = Veterinario::where('email', $request->email)->first();

        // Verifica se o veterinário existe e se a senha bate com a criptografada
        if (! $veterinario || ! Hash::check($request->password, $veterinario->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        // Cria o "Token VIP" (Pulseira de acesso)
        $token = $veterinario->createToken('vetflow-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $token,
            'veterinario' => $veterinario
        ]);
    }

    public function logout(Request $request)
    {
        // Revoga o token atual (retira a pulseira)
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}