<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TutorController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\VeterinarioController;
use App\Http\Controllers\AuthController;

use App\Http\Middleware\EnsureVeterinarioVerificado;

/*
|--------------------------------------------------------------------------
| 1. Rotas públicas
|--------------------------------------------------------------------------
|
| Não exigem autenticação.
|
*/

// Login de veterinário
Route::post(
    '/login',
    [AuthController::class, 'login']
);

// Cadastro de veterinário
Route::post(
    '/register',
    [AuthController::class, 'register']
);

// Busca pública de veterinários aprovados
Route::get(
    '/veterinarios/buscar',
    [VeterinarioController::class, 'index']
);

/*
|--------------------------------------------------------------------------
| 2. Rotas autenticadas
|--------------------------------------------------------------------------
|
| Exigem token válido, mas não exigem
| que o CRMV esteja aprovado.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    // Permite que o veterinário encerre sua sessão
    // mesmo enquanto aguarda aprovação do CRMV.
    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );

    /*
    |--------------------------------------------------------------------------
    | 3. Rotas profissionais
    |--------------------------------------------------------------------------
    |
    | Exigem autenticação e autorização profissional.
    |
    */

    Route::middleware(
        EnsureVeterinarioVerificado::class
    )->group(function () {

        // Gestão de veterinários
        Route::apiResource(
            'veterinarios',
            VeterinarioController::class
        );

        // Gestão de tutores
        Route::apiResource(
            'tutores',
            TutorController::class
        );

        // Gestão de pets
        Route::apiResource(
            'pets',
            PetController::class
        );

        // Gestão de agendamentos
        Route::apiResource(
            'agendamentos',
            AgendamentoController::class
        );

    });
});
