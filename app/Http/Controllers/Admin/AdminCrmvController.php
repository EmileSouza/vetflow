<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Veterinario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminCrmvController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Listar CRMVs pendentes
    |--------------------------------------------------------------------------
    */

    public function pendentes(): JsonResponse
    {
        $veterinarios = Veterinario::query()
            ->where(
                'status_crmv',
                Veterinario::CRMV_PENDENTE
            )
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate(10, [
                'id',
                'nome',
                'crmv',
                'crmv_uf',
                'cidade',
                'uf',
                'especialidade',
                'ativo',
                'created_at',
            ]);

        return response()->json([
            'data' => $veterinarios->items(),

            'meta' => [
                'pagina_atual' => $veterinarios->currentPage(),
                'ultima_pagina' => $veterinarios->lastPage(),
                'por_pagina' => $veterinarios->perPage(),
                'total' => $veterinarios->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Aprovar CRMV
    |--------------------------------------------------------------------------
    */

    public function aprovar(
        Request $request,
        int $id
    ): JsonResponse {

        // Valida os dados recebidos.
        $validador = Validator::make($request->all(), [
            'metodo_verificacao' => [
                'required',
                Rule::in(['manual', 'simulado']),
            ],

            'crmv_uf' => [
                'required',
                'string',
                'size:2',
            ],

            'fonte_verificacao' => [
                'required_if:metodo_verificacao,manual',
                'nullable',
                'string',
                'max:255',
            ],

            'consulta_confirmada' => [
                'accepted_if:metodo_verificacao,manual',
            ],
        ]);

        // Retorna erro 422 em JSON quando os dados
        // não atendem às regras de validação.
        if ($validador->fails()) {
            return response()->json([
                'message' => 'Os dados informados são inválidos.',
                'errors' => $validador->errors()->toArray(),
            ], 422);
        }

        // Recupera somente os campos validados.
        $dados = $validador->validated();

        $metodo = $dados['metodo_verificacao'];

        // Impede simulação fora do desenvolvimento/testes.
        if (
            $metodo === 'simulado' &&
            ! app()->environment('local', 'testing')
        ) {
            return response()->json([
                'message' =>
                    'Verificação simulada não permitida neste ambiente.',
            ], 403);
        }

        // Transação para proteger contra decisões simultâneas.
        return DB::transaction(function () use (
            $request,
            $id,
            $dados,
            $metodo
        ): JsonResponse {

            $veterinario = Veterinario::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            // Apenas registros pendentes podem ser aprovados.
            if (
                $veterinario->status_crmv !==
                Veterinario::CRMV_PENDENTE
            ) {
                return response()->json([
                    'message' => 'Este CRMV já foi analisado.',
                ], 409);
            }

            // Simulação exige identificação de teste.
            if (
                $metodo === 'simulado' &&
                ! str_starts_with(
                    strtoupper($veterinario->crmv),
                    'TESTE-'
                )
            ) {
                return response()->json([
                    'message' =>
                        'Use um CRMV fictício iniciado por TESTE- para simulações.',
                ], 422);
            }

            // Registra a aprovação.
            $veterinario->status_crmv =
                Veterinario::CRMV_APROVADO;

            $veterinario->crmv_uf =
                strtoupper($dados['crmv_uf']);

            $veterinario->metodo_verificacao = $metodo;

            $veterinario->fonte_verificacao =
                $metodo === 'manual'
                    ? $dados['fonte_verificacao']
                    : null;

            $veterinario->crmv_verificado_em = now();

            $veterinario->crmv_verificado_por =
                $request->user('admin')->id;

            $veterinario->motivo_rejeicao_crmv = null;

            $veterinario->save();

            return response()->json([
                'message' => 'CRMV aprovado com sucesso!',
                'data' => [
                    'id' => $veterinario->id,
                    'status_crmv' =>
                        $veterinario->status_crmv,
                    'metodo_verificacao' =>
                        $veterinario->metodo_verificacao,
                ],
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Rejeitar CRMV
    |--------------------------------------------------------------------------
    */

    public function rejeitar(
        Request $request,
        int $id
    ): JsonResponse {

        // Rejeição exige justificativa.
        $dados = $request->validate([
            'motivo_rejeicao_crmv' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],

            'fonte_verificacao' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        return DB::transaction(function () use (
            $request,
            $id,
            $dados
        ): JsonResponse {

            $veterinario = Veterinario::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            // Impede decisões duplicadas.
            if (
                $veterinario->status_crmv !==
                Veterinario::CRMV_PENDENTE
            ) {
                return response()->json([
                    'message' => 'Este CRMV já foi analisado.',
                ], 409);
            }

            // Registra a rejeição.
            $veterinario->status_crmv =
                Veterinario::CRMV_REJEITADO;

            $veterinario->motivo_rejeicao_crmv =
                $dados['motivo_rejeicao_crmv'];

            $veterinario->metodo_verificacao = 'manual';

            $veterinario->fonte_verificacao =
                $dados['fonte_verificacao'] ?? null;

            $veterinario->crmv_verificado_em = now();

            $veterinario->crmv_verificado_por =
                $request->user('admin')->id;

            $veterinario->save();

            return response()->json([
                'message' => 'CRMV rejeitado com sucesso.',
                'data' => [
                    'id' => $veterinario->id,
                    'status_crmv' =>
                        $veterinario->status_crmv,
                ],
            ]);
        });
    }
}
