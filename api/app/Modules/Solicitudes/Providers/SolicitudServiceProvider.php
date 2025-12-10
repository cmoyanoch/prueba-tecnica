<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Providers;

use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use App\Modules\Solicitudes\Infrastructure\Repositories\EloquentSolicitudRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SolicitudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings del módulo
        $this->app->bind(
            SolicitudRepositoryInterface::class,
            EloquentSolicitudRepository::class
        );

    }

    public function boot(): void
    {
        // Cargar migraciones del módulo
        $this->loadMigrationsFrom(
            __DIR__ . '/../Database/Migrations'
        );

        // Cargar factories del módulo
        $this->loadFactoriesFrom(
            __DIR__ . '/../Database/Factories'
        );

        // Registrar rutas del módulo bajo el prefijo 'api'
        Route::prefix('api')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
            });
    }
}
