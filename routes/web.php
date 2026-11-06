<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Middleware\EnsureAdminAccess;

// Landing page e aplicação Vue
Route::get('/', function () {
    return view('app');
});

// Login administrativo via sessão
Route::post(
    '/admin/session',
    [AdminSessionController::class, 'login']
);

// Logout administrativo
Route::delete(
    '/admin/session',
    [AdminSessionController::class, 'logout']
)->middleware('auth:admin');

// Retorna informações da conta administrativa autenticada
Route::get('/admin/me', function (Request $request) {
    $usuario = $request->user('admin');

    return response()->json([
        'id' => $usuario->id,
        'nome' => $usuario->name,
        'papel' => $usuario->papel,
    ]);
})->middleware(EnsureAdminAccess::class);

// Mantém as demais páginas Vue funcionando
Route::view('/{any?}', 'app')->where('any', '.*');
