<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Laravel;

use Illuminate\Support\Facades\Log;
use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Implementación del logger de auditoría usando Laravel Log.
 */
final class LaravelAuditLogger implements AuditLoggerInterface
{
    public function __construct(
        private readonly string $channel = 'solicitudes'
    ) {}

    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void
    {
        Log::channel($this->channel)->info('Solicitud creada', [
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
        ]);
    }

    public function logEstadoChanged(
        int $solicitudId,
        EstadoSolicitud $estadoAnterior,
        EstadoSolicitud $estadoNuevo
    ): void {
        Log::channel($this->channel)->info('Estado de solicitud cambiado', [
            'solicitud_id' => $solicitudId,
            'estado_anterior' => $estadoAnterior->value,
            'estado_nuevo' => $estadoNuevo->value,
        ]);
    }

    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void
    {
        Log::channel($this->channel)->info('Solicitud eliminada', [
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
        ]);
    }

    public function logError(string $operation, string $message, array $context = []): void
    {
        Log::channel($this->channel)->error('Error en operación de solicitud', [
            'operation' => $operation,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
