<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\VeterinarioController;
use App\Http\Controllers\AuthController;

// Rota PÚBLICA (Qualquer um pode tentar fazer login)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Grupo de Rotas PROTEGIDAS (Só entra com Token válido)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rota para o próprio veterinário se deslogar
    Route::post('/logout', [AuthController::class, 'logout']);

    // Nossas APIs de negócio
    Route::apiResource('veterinarios', VeterinarioController::class);
    Route::apiResource('tutores', TutorController::class);
    Route::apiResource('pets', PetController::class);
    Route::apiResource('agendamentos', AgendamentoController::class);
});