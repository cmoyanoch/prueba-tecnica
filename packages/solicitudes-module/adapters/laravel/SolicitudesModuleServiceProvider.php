<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Laravel;

use Illuminate\Support\ServiceProvider;
use SolicitudesModule\Application\UseCases\CreateSolicitudUseCase;
use SolicitudesModule\Application\UseCases\DeleteSolicitudUseCase;
use SolicitudesModule\Application\UseCases\GetSolicitudUseCase;
use SolicitudesModule\Application\UseCases\ListSolicitudesUseCase;
use SolicitudesModule\Application\UseCases\UpdateEstadoSolicitudUseCase;
use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Infrastructure\Persistence\Eloquent\EloquentSolicitudModel;
use SolicitudesModule\Infrastructure\Persistence\Eloquent\EloquentSolicitudRepository;
use SolicitudesModule\Infrastructure\Services\NullAuditLogger;
use SolicitudesModule\Infrastructure\Services\NullEventDispatcher;

/**
 * Service Provider para integrar el módulo Solicitudes en Laravel.
 */
class SolicitudesModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar el modelo Eloquent
        $this->app->bind(EloquentSolicitudModel::class, function () {
            return new EloquentSolicitudModel();
        });

        // Registrar el repositorio
        $this->app->bind(
            SolicitudRepositoryInterface::class,
            EloquentSolicitudRepository::class
        );

        // Registrar servicios auxiliares (se pueden sobrescribir)
        $this->app->singletonIf(
            AuditLoggerInterface::class,
            NullAuditLogger::class
        );

        $this->app->singletonIf(
            EventDispatcherInterface::class,
            NullEventDispatcher::class
        );

        // Registrar Use Cases
        $this->app->bind(CreateSolicitudUseCase::class, function ($app) {
            return new CreateSolicitudUseCase(
                $app->make(SolicitudRepositoryInterface::class),
                $app->make(AuditLoggerInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });

        $this->app->bind(ListSolicitudesUseCase::class, function ($app) {
            return new ListSolicitudesUseCase(
                $app->make(SolicitudRepositoryInterface::class)
            );
        });

        $this->app->bind(GetSolicitudUseCase::class, function ($app) {
            return new GetSolicitudUseCase(
                $app->make(SolicitudRepositoryInterface::class)
            );
        });

        $this->app->bind(UpdateEstadoSolicitudUseCase::class, function ($app) {
            return new UpdateEstadoSolicitudUseCase(
                $app->make(SolicitudRepositoryInterface::class),
                $app->make(AuditLoggerInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });

        $this->app->bind(DeleteSolicitudUseCase::class, function ($app) {
            return new DeleteSolicitudUseCase(
                $app->make(SolicitudRepositoryInterface::class),
                $app->make(AuditLoggerInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });
    }

    public function boot(): void
    {
        // Cargar migraciones
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Cargar rutas si existen
        if (file_exists(__DIR__ . '/routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }

        // Publicar configuración
        $this->publishes([
            __DIR__ . '/config/solicitudes.php' => config_path('solicitudes.php'),
        ], 'solicitudes-config');

        // Publicar migraciones
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'solicitudes-migrations');
    }
}
