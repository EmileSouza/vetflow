<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCrmvController;
use App\Http\Controllers\Admin\AdminVeterinarioController;
use App\Http\Middleware\EnsureAdminAccess;

/*
|--------------------------------------------------------------------------
| Aplicação Vue
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('app');
});

/*
|--------------------------------------------------------------------------
| Autenticação administrativa
|--------------------------------------------------------------------------
*/

Route::post(
    '/admin/session',
    [AdminSessionController::class, 'login']
);

Route::delete(
    '/admin/session',
    [AdminSessionController::class, 'logout']
)->middleware('auth:admin');

Route::get('/admin/me', function (Request $request) {
    $usuario = $request->user('admin');

    return response()->json([
        'id' => $usuario->id,
        'nome' => $usuario->name,
        'papel' => $usuario->papel,
    ]);
})->middleware(EnsureAdminAccess::class);

/*
|--------------------------------------------------------------------------
| Dashboard administrativo
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/dashboard/resumo',
    [AdminDashboardController::class, 'resumo']
)->middleware(EnsureAdminAccess::class);

Route::get(
    '/admin/dashboard/agendamentos-por-periodo',
    [
        AdminDashboardController::class,
        'agendamentosPorPeriodo'
    ]
)->middleware(EnsureAdminAccess::class);

/*
|--------------------------------------------------------------------------
| NOVO - Gestão de veterinários
|--------------------------------------------------------------------------
|
| Apenas administradores poderão acessar
| os dados individuais dos profissionais.
|
*/

// Listar todos os veterinários.
Route::get(
    '/admin/veterinarios',
    [AdminVeterinarioController::class, 'index']
)->middleware(
    EnsureAdminAccess::class . ':aprovar-crmv'
);

// Consultar detalhes de um veterinário.
Route::get(
    '/admin/veterinarios/{id}',
    [AdminVeterinarioController::class, 'show']
)->whereNumber('id')
  ->middleware(
      EnsureAdminAccess::class . ':aprovar-crmv'
  );

/*
|--------------------------------------------------------------------------
| Verificação de CRMV
|--------------------------------------------------------------------------
*/

// Listar CRMVs pendentes.
Route::get(
    '/admin/crmv/pendentes',
    [AdminCrmvController::class, 'pendentes']
)->middleware(
    EnsureAdminAccess::class . ':aprovar-crmv'
);

// Aprovar CRMV.
Route::patch(
    '/admin/crmv/{id}/aprovar',
    [AdminCrmvController::class, 'aprovar']
)->whereNumber('id')
  ->middleware(
      EnsureAdminAccess::class . ':aprovar-crmv'
  );

// Rejeitar CRMV.
Route::patch(
    '/admin/crmv/{id}/rejeitar',
    [AdminCrmvController::class, 'rejeitar']
)->whereNumber('id')
  ->middleware(
      EnsureAdminAccess::class . ':aprovar-crmv'
  );

/*
|--------------------------------------------------------------------------
| Navegação do Vue
|--------------------------------------------------------------------------
|
| Esta rota deve permanecer por último.
|
*/

Route::view('/{any?}', 'app')
    ->where('any', '.*');
