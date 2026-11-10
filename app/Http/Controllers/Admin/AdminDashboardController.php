<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Veterinario;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Indicadores gerais do Dashboard
    |--------------------------------------------------------------------------
    */

    public function resumo(): JsonResponse
    {
        return response()->json([
            'data' => [
                'veterinarios_total' => DB::table(
                    'veterinarios'
                )->count(),

                'crmv_pendentes' => Veterinario::where(
                    'status_crmv',
                    Veterinario::CRMV_PENDENTE
                )->count(),

                'crmv_aprovados' => Veterinario::where(
                    'status_crmv',
                    Veterinario::CRMV_APROVADO
                )->count(),

                'crmv_rejeitados' => Veterinario::where(
                    'status_crmv',
                    Veterinario::CRMV_REJEITADO
                )->count(),

                'tutores_total' => DB::table(
                    'tutores'
                )->count(),

                'pets_total' => DB::table(
                    'pets'
                )->count(),

                'agendamentos_total' => DB::table(
                    'agendamentos'
                )->count(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. NOVO - Agendamentos por período
    |--------------------------------------------------------------------------
    |
    | Consulta as datas das consultas veterinárias
    | nos últimos 28 dias, incluindo hoje.
    |
    | Cada coluna representa sete dias.
    |
    */

    public function agendamentosPorPeriodo(): JsonResponse
    {
        // Primeiro dia: 27 dias antes de hoje.
        $hoje = now()->startOfDay();

        $semanas = [];
        $totalPeriodo = 0;

        // Criar quatro grupos de sete dias.
        for ($indice = 0; $indice < 4; $indice++) {

            // A primeira semana começa 27 dias atrás.
            // A quarta semana termina hoje.
            $inicio = $hoje->copy()->subDays(
                27 - ($indice * 7)
            );

            $fim = $inicio->copy()
                ->addDays(6)
                ->endOfDay();

            // Conta somente consultas cuja data_hora
            // pertence à semana correspondente.
            $quantidade = DB::table('agendamentos')
                ->whereBetween('data_hora', [
                    $inicio->toDateTimeString(),
                    $fim->toDateTimeString(),
                ])
                ->count();

            $semanas[] = [
                'rotulo' => 'Sem ' . ($indice + 1),
                'inicio' => $inicio->toDateString(),
                'fim' => $fim->toDateString(),
                'quantidade' => $quantidade,
            ];

            $totalPeriodo += $quantidade;
        }

        return response()->json([
            'data' => [
                'periodo' => 'Últimos 28 dias',
                'total_periodo' => $totalPeriodo,
                'semanas' => $semanas,
            ],
        ]);
    }
}
