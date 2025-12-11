<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Services;

use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Implementación del logger de auditoría que escribe a archivo.
 * PHP puro - Sin dependencias de framework.
 */
final class FileAuditLogger implements AuditLoggerInterface
{
    public function __construct(
        private readonly string $logPath
    ) {
        $dir = dirname($logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void
    {
        $this->log('solicitud.created', [
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
        ]);
    }

    public function logEstadoChanged(
        int $solicitudId,
        EstadoSolicitud $estadoAnterior,
        EstadoSolicitud $estadoNuevo
    ): void {
        $this->log('solicitud.estado_changed', [
            'solicitud_id' => $solicitudId,
            'estado_anterior' => $estadoAnterior->value,
            'estado_nuevo' => $estadoNuevo->value,
        ]);
    }

    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void
    {
        $this->log('solicitud.deleted', [
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
        ]);
    }

    public function logError(string $operation, string $message, array $context = []): void
    {
        $this->log('solicitud.error', [
            'operation' => $operation,
            'message' => $message,
            'context' => $context,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function log(string $event, array $data): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'data' => $data,
        ];

        $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        file_put_contents($this->logPath, $line, FILE_APPEND | LOCK_EX);
    }
}
