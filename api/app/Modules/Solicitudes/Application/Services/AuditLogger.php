<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\Services;

use Illuminate\Support\Facades\Log;

final class AuditLogger implements AuditLoggerInterface
{
    private const CHANNEL = 'audit';

    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void
    {
        Log::channel(self::CHANNEL)->info('Solicitud creada', [
            'action' => 'solicitud.created',
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function logEstadoUpdated(int $solicitudId, string $estadoAnterior, string $estadoNuevo): void
    {
        Log::channel(self::CHANNEL)->info('Estado de solicitud actualizado', [
            'action' => 'solicitud.estado.updated',
            'solicitud_id' => $solicitudId,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function logSolicitudesListed(int $count): void
    {
        Log::channel(self::CHANNEL)->info('Solicitudes listadas', [
            'action' => 'solicitudes.listed',
            'count' => $count,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function logError(string $action, string $message, array $context = []): void
    {
        Log::channel(self::CHANNEL)->error('Error en operación de solicitud', [
            'action' => $action,
            'error' => $message,
            'context' => $context,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void
    {
        Log::channel(self::CHANNEL)->info('Solicitud eliminada', [
            'action' => 'solicitud.deleted',
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
