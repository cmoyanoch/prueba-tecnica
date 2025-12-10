<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\Actions;

use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;

final readonly class UpdateEstadoSolicitudAction
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger
    ) {}

    public function execute(int $id, EstadoSolicitud $estado): object
    {
        // Obtener estado anterior antes de actualizar
        $solicitudAnterior = $this->repository->findById($id);
        $estadoAnterior = $solicitudAnterior->estado->value;

        // Actualizar estado
        $solicitud = $this->repository->updateEstado($id, $estado);

        // Log de auditoría
        $this->auditLogger->logEstadoUpdated(
            $id,
            $estadoAnterior,
            $estado->value
        );

        return $solicitud;
    }
}
