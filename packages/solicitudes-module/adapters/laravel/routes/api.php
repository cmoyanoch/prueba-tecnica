<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use SolicitudesModule\Adapters\Laravel\Http\SolicitudController;

/*
|--------------------------------------------------------------------------
| API Routes para el Módulo Solicitudes
|--------------------------------------------------------------------------
*/

Route::prefix(config('solicitudes.api_prefix', 'api/solicitudes'))
    ->middleware(config('solicitudes.middleware', ['api']))
    ->group(function () {
        Route::get('/', [SolicitudController::class, 'index'])->name('solicitudes.index');
        Route::get('/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
        Route::post('/', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::put('/{id}', [SolicitudController::class, 'update'])->name('solicitudes.update');
        Route::patch('/{id}', [SolicitudController::class, 'update'])->name('solicitudes.patch');
        Route::delete('/{id}', [SolicitudController::class, 'destroy'])->name('solicitudes.destroy');
    });
