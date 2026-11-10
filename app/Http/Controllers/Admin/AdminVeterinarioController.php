<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Veterinario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminVeterinarioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Listar todos os veterinários
    |--------------------------------------------------------------------------
    |
    | Permite pesquisa, filtros e paginação.
    | A autorização é definida nas rotas.
    |
    */

    public function index(Request $request): JsonResponse
    {
        // Validar filtros enviados na URL.
        $validador = Validator::make($request->query(), [
            'busca' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'status_crmv' => [
                'sometimes',
                'nullable',
                Rule::in([
                    Veterinario::CRMV_PENDENTE,
                    Veterinario::CRMV_APROVADO,
                    Veterinario::CRMV_REJEITADO,
                ]),
            ],

            'uf' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[A-Za-z]{2}$/',
            ],

            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
        ]);

        if ($validador->fails()) {
            return response()->json([
                'message' => 'Filtros inválidos.',
                'errors' => $validador->errors()->toArray(),
            ], 422);
        }

        $filtros = $validador->validated();

        $busca = trim($filtros['busca'] ?? '');
        $status = $filtros['status_crmv'] ?? null;

        $uf = isset($filtros['uf'])
            ? strtoupper($filtros['uf'])
            : null;

        // Diferentemente da fila de CRMV,
        // esta consulta não limita o status.
        $consulta = Veterinario::query();

        // Pesquisa por nome OU CRMV.
        if ($busca !== '') {
            $consulta->where(function ($query) use ($busca) {
                $query
                    ->where('nome', 'like', "%{$busca}%")
                    ->orWhere('crmv', 'like', "%{$busca}%");
            });
        }

        // Filtrar por status.
        if ($status) {
            $consulta->where('status_crmv', $status);
        }

        // UF da localização cadastrada.
        // A UF do registro CRMV é exibida separadamente.
        if ($uf) {
            $consulta->where('uf', $uf);
        }

        // Ordenar cadastros mais recentes primeiro.
        $veterinarios = $consulta
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10, [
                'id',
                'nome',
                'crmv',
                'crmv_uf',
                'cidade',
                'uf',
                'especialidade',
                'ativo',
                'status_crmv',
                'created_at',
            ]);

        return response()->json([
            'data' => $veterinarios->items(),

            'meta' => [
                'pagina_atual' =>
                    $veterinarios->currentPage(),

                'ultima_pagina' =>
                    $veterinarios->lastPage(),

                'por_pagina' =>
                    $veterinarios->perPage(),

                'total' =>
                    $veterinarios->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Detalhes de um veterinário
    |--------------------------------------------------------------------------
    |
    | Disponibiliza os dados necessários
    | para acompanhar a verificação de CRMV.
    |
    | Não expõe senha, telefone nem e-mail.
    |
    */

    public function show(int $id): JsonResponse
    {
        $veterinario = Veterinario::query()
            ->select([
                'id',
                'nome',
                'crmv',
                'crmv_uf',
                'cidade',
                'uf',
                'especialidade',
                'ativo',
                'status_crmv',
                'metodo_verificacao',
                'fonte_verificacao',
                'crmv_verificado_em',
                'crmv_verificado_por',
                'motivo_rejeicao_crmv',
                'created_at',
            ])
            ->with('verificadoPor:id,name')
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $veterinario->id,
                'nome' => $veterinario->nome,
                'crmv' => $veterinario->crmv,
                'crmv_uf' => $veterinario->crmv_uf,

                'cidade' => $veterinario->cidade,
                'uf' => $veterinario->uf,

                'especialidade' =>
                    $veterinario->especialidade,

                'ativo' => $veterinario->ativo,

                'status_crmv' =>
                    $veterinario->status_crmv,

                'metodo_verificacao' =>
                    $veterinario->metodo_verificacao,

                'fonte_verificacao' =>
                    $veterinario->fonte_verificacao,

                'crmv_verificado_em' =>
                    $veterinario->crmv_verificado_em,

                'verificador_nome' =>
                    $veterinario->verificadoPor?->name,

                'motivo_rejeicao_crmv' =>
                    $veterinario->motivo_rejeicao_crmv,

                'created_at' =>
                    $veterinario->created_at,
            ],
        ]);
    }
}
