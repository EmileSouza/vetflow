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

        // Buscar somente veterinários ativos
        // com CRMV aprovado.
        $query = Veterinario::query()
            ->where('ativo', true)
            ->where(
                'status_crmv',
                Veterinario::CRMV_APROVADO
            )
            ->when(
                ! app()->environment('local', 'testing'),
                function ($query) {
                    $query->where(
                        'metodo_verificacao',
                        'manual'
                    );
                }
            )
            ->where('cidade', $request->cidade);

        // Filtrar pelo estado, quando informado.
        if ($request->filled('uf')) {
            $query->where(
                'uf',
                strtoupper($request->uf)
            );
        }

        // Selecionar somente os dados públicos.
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
            'data' => $veterinarios,
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
            'password' => 'required|string|min:6|confirmed',
            'crmv' => 'required|string|max:50|unique:veterinarios',
            'telefone' => 'nullable|string|max:20',
            'especialidade' => 'nullable|string|max:255',
        ]);

        // O Model Veterinario utiliza o cast "hashed"
        // para armazenar a senha com segurança.
        $veterinario = Veterinario::create($validated);

        // Remover a senha da resposta.
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
