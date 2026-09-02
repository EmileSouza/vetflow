<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validated = $request->validate([
        'data_hora' => 'required|date',
        'veterinario_id' => [
            'required',
            'exists:veterinarios,id',
            // A Regra de Ouro: verifica se este veterinário JÁ tem agendamento neste horário
            Rule::unique('agendamentos')->where(function ($query) use ($request) {
                return $query->where('data_hora', $request->data_hora);
            })
        ],
        'status' => 'nullable|in:pendente,confirmado,concluído,cancelado',
        'pet_id' => 'required|exists:pets,id',
    ]);

    $agendamento = Agendamento::create($validated);

    return response()->json($agendamento, 201);
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
