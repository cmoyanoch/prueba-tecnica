<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Symfony;

use Psr\Log\LoggerInterface;
use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Implementación del logger de auditoría usando Symfony Logger.
 */
final class SymfonyAuditLogger implements AuditLoggerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void
    {
        $this->logger->info('Solicitud creada', [
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
        ]);
    }

    public function logEstadoChanged(
        int $solicitudId,
        EstadoSolicitud $estadoAnterior,
        EstadoSolicitud $estadoNuevo
    ): void {
        $this->logger->info('Estado de solicitud cambiado', [
            'solicitud_id' => $solicitudId,
            'estado_anterior' => $estadoAnterior->value,
            'estado_nuevo' => $estadoNuevo->value,
        ]);
    }

    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void
    {
        $this->logger->info('Solicitud eliminada', [
            'solicitud_id' => $solicitudId,
            'nombre_documento' => $nombreDocumento,
        ]);
    }

    public function logError(string $operation, string $message, array $context = []): void
    {
        $this->logger->error('Error en operación de solicitud', [
            'operation' => $operation,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
