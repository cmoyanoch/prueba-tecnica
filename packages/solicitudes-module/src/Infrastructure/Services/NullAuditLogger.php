<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Services;

use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Implementación nula del logger de auditoría.
 * Útil para testing o cuando no se necesita auditoría.
 */
final class NullAuditLogger implements AuditLoggerInterface
{
    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void
    {
        // No-op
    }

    public function logEstadoChanged(
        int $solicitudId,
        EstadoSolicitud $estadoAnterior,
        EstadoSolicitud $estadoNuevo
    ): void {
        // No-op
    }

    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void
    {
        // No-op
    }

    public function logError(string $operation, string $message, array $context = []): void
    {
        // No-op
    }
}
