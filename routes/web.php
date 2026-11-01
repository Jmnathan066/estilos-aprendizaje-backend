<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['servicio' => 'API Estilos de Aprendizaje', 'ok' => true]));
