<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaballoController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | CABALLOS
    |--------------------------------------------------------------------------
    */

    Route::get('/caballos', [CaballoController::class, 'index']);
    Route::get('/caballos/{id}', [CaballoController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | RESERVAS
    |--------------------------------------------------------------------------
    */

    Route::get('/reservas', [ReservaController::class, 'index']);
    Route::get('/reservas/{id}', [ReservaController::class, 'show']);
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::put('/reservas/{id}', [ReservaController::class, 'update']);
    Route::delete('/reservas/{id}', [ReservaController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | DISPONIBILIDAD / CALENDARIO (🔥 PRO+)
    |--------------------------------------------------------------------------
    */

    Route::get('/disponibilidad', [ReservaController::class, 'disponibilidad']);

    Route::get('/calendario', [ReservaController::class, 'calendario']);

    Route::get('/calendario/semana', [ReservaController::class, 'calendarioSemana']);

    /*
    |--------------------------------------------------------------------------
    | PAGOS
    |--------------------------------------------------------------------------
    */

    Route::get('/pagos', [PagoController::class, 'index']);
    Route::post('/pagos', [PagoController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | CABALLOS (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::post('/caballos', [CaballoController::class, 'store']);
        Route::put('/caballos/{id}', [CaballoController::class, 'update']);
        Route::delete('/caballos/{id}', [CaballoController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | RESERVAS (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/reservas', [ReservaController::class, 'adminIndex']);

        Route::put('/admin/reservas/{id}/estado', [
            AdminController::class,
            'cambiarEstadoReserva'
        ]);

        /*
        🔥 BLOQUEO AUTOMÁTICO (manual endpoint)
        */
        Route::post('/admin/reservas/cancelar-pendientes', [
            AdminController::class,
            'cancelarReservasPendientes'
        ]);

        /*
        |--------------------------------------------------------------------------
        | PAGOS (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/pagos', [PagoController::class, 'adminIndex']);

        /*
        |--------------------------------------------------------------------------
        | USUARIOS (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/usuarios', [AdminController::class, 'usuarios']);

        /*
        |--------------------------------------------------------------------------
        | ESTADÍSTICAS (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/estadisticas', [AdminController::class, 'estadisticas']);

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    });
});