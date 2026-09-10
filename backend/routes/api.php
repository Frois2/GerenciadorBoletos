<?php

use App\Http\Controllers\Api\BoletoController;
use App\Http\Controllers\Api\ClienteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('clientes', ClienteController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);

Route::apiResource('boletos', BoletoController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
