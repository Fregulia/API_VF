<?php

use App\Http\Controllers\api\AtletaController;
use App\Http\Controllers\api\EsporteController;
use App\Http\Controllers\api\TreinadorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return response()->json(['message' => 'Use POST /api/login for authentication']);
})->name('login');

Route::resource('treinador', TreinadorController::class);
Route::resource('atleta', AtletaController::class) -> parameters(["atletum" => 'atleta']); 
Route::resource('esporte', EsporteController::class);
