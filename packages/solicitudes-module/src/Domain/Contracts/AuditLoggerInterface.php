<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Contracts;

use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Contrato para el logger de auditoría.
 * Permite registrar acciones sobre las solicitudes.
 */
interface AuditLoggerInterface
{
    /**
     * Registra la creación de una solicitud.
     */
    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void;

    /**
     * Registra el cambio de estado de una solicitud.
     */
    public function logEstadoChanged(
        int $solicitudId,
        EstadoSolicitud $estadoAnterior,
        EstadoSolicitud $estadoNuevo
    ): void;

    /**
     * Registra la eliminación de una solicitud.
     */
    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void;

    /**
     * Registra un error en una operación.
     */
    public function logError(string $operation, string $message, array $context = []): void;
}
