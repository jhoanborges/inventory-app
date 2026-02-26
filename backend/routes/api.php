<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoteController;
use App\Http\Controllers\Api\MovimientoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RutaController;
use App\Http\Controllers\Api\ScanController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::apiResource('productos', ProductoController::class);
    Route::post('/productos/sync', [ProductoController::class, 'sync']);

    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('rutas', RutaController::class);

    Route::get('/movimientos', [MovimientoController::class, 'index']);
    Route::post('/movimientos', [MovimientoController::class, 'store']);

    Route::get('/scan/{barcode}', [ScanController::class, 'scan']);
});
