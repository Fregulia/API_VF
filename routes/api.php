<?php

use App\Http\Controllers\api\AtletaController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\EsporteController;
use App\Http\Controllers\api\TreinadorController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/revoke-token', [AuthController::class, 'revokeToken']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas de usuários
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('ownership');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('ownership');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('ownership');
});

// Recursos com controle de acesso
Route::apiResource('treinador', TreinadorController::class)->except(['store', 'update', 'destroy']);
Route::apiResource('atleta', AtletaController::class)->except(['store', 'update', 'destroy']);
Route::apiResource('esporte', EsporteController::class)->except(['store', 'update', 'destroy']);

// Rotas protegidas para modificação
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::post('/treinador', [TreinadorController::class, 'store']);
    Route::put('/treinador/{treinador}', [TreinadorController::class, 'update']);
    Route::delete('/treinador/{treinador}', [TreinadorController::class, 'destroy']);

    Route::post('/atleta', [AtletaController::class, 'store']);
    Route::put('/atleta/{atleta}', [AtletaController::class, 'update']);
    Route::delete('/atleta/{atleta}', [AtletaController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/esporte', [EsporteController::class, 'store']);
    Route::put('/esporte/{esporte}', [EsporteController::class, 'update']);
    Route::delete('/esporte/{esporte}', [EsporteController::class, 'destroy']);
});
