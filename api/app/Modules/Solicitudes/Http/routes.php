<?php

declare(strict_types=1);

use App\Modules\Solicitudes\Http\Controllers\SolicitudController;
use App\Modules\Solicitudes\Http\Middleware\EnsureRequestIsAjax;
use Illuminate\Support\Facades\Route;

Route::prefix('solicitudes')
    ->middleware([EnsureRequestIsAjax::class])
    ->group(function () {
        Route::get('/', [SolicitudController::class, 'index'])
            ->name('solicitudes.index');

        Route::post('/', [SolicitudController::class, 'store'])
            ->name('solicitudes.store');

        Route::patch('/{id}', [SolicitudController::class, 'update'])
            ->name('solicitudes.update')
            ->whereNumber('id');

        Route::delete('/{id}', [SolicitudController::class, 'destroy'])
            ->name('solicitudes.destroy')
            ->whereNumber('id');
    });
