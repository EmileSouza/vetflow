<?php

namespace App\Http\Controllers;

use App\Models\Veterinario;
use Illuminate\Http\Request;

class VeterinarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    $request->validate([
        'cidade' => 'required|string|max:100',
        'uf' => 'nullable|string|size:2',
    ]);

    $query = Veterinario::query()
        ->where('ativo', true)
        ->where('cidade', $request->cidade);

    if ($request->filled('uf')) {
        $query->where('uf', strtoupper($request->uf));
    }

    $veterinarios = $query
        ->select([
            'id',
            'nome',
            'crmv',
            'especialidade',
            'cidade',
            'uf',
        ])
        ->orderBy('nome')
        ->get();

    return response()->json([
        'data' => $veterinarios
    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:veterinarios',
        // 'confirmed' exige que o frontend envie 'password' e 'password_confirmation' iguais
        'password' => 'required|string|min:6|confirmed', 
        'crmv' => 'required|string|max:50|unique:veterinarios',
        'telefone' => 'nullable|string|max:20',
        'especialidade' => 'nullable|string|max:255',
    ]);

    // LEMBRETE: No Model Veterinario, configuramos 'password' => 'hashed'.
    // Isso significa que o Laravel criptografa a senha AUTOMATICAMENTE aqui!
    $veterinario = Veterinario::create($validated);

    // Por segurança, removemos a senha do JSON de resposta
    unset($veterinario->password);

    return response()->json($veterinario, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
