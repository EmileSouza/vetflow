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

        if (! $veterinario || ! Hash::check($request->password, $veterinario->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $token = $veterinario->createToken('vetflow-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $token,
            'veterinario' => $veterinario
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:veterinarios,email',
            'password' => 'required|string|min:8|confirmed',
            'crmv' => 'required|string|max:50|unique:veterinarios,crmv',
            'telefone' => 'nullable|string|max:30',
            'especialidade' => 'nullable|string|max:255',
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|size:2',
        ]);

        $veterinario = Veterinario::create($validated);

        $token = $veterinario->createToken('vetflow-token')->plainTextToken;

        return response()->json([
            'message' => 'Cadastro realizado com sucesso!',
            'token' => $token,
            'veterinario' => $veterinario
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Deslogado com sucesso!'
        ]);
    }
}