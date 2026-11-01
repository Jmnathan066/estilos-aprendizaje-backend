<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EncuestaController;
use App\Http\Controllers\Api\ProfesorController;
use Illuminate\Support\Facades\Route;

// ---- Estudiantes (público, sin sesión) ----
Route::post('/encuesta', [EncuestaController::class, 'guardar'])->middleware('throttle:30,1');
Route::get('/resultado/{token}', [EncuestaController::class, 'resultado']);
Route::post('/resultado/{token}/correo', [EncuestaController::class, 'correo'])->middleware('throttle:10,1');

// ---- Profesor ----
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('token.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profesor/estudiantes', [ProfesorController::class, 'index']);
    Route::get('/profesor/excel', [ProfesorController::class, 'excel']);
});
