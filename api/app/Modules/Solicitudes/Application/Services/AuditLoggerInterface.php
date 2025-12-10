<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\Services;

interface AuditLoggerInterface
{
    public function logSolicitudCreated(int $solicitudId, string $nombreDocumento): void;

    public function logEstadoUpdated(int $solicitudId, string $estadoAnterior, string $estadoNuevo): void;

    public function logSolicitudesListed(int $count): void;

    public function logError(string $action, string $message, array $context = []): void;

    public function logSolicitudDeleted(int $solicitudId, string $nombreDocumento): void;
}
