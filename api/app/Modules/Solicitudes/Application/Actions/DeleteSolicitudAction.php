<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\Actions;

use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;

final readonly class DeleteSolicitudAction
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger
    ) {}

    public function execute(int $id): bool
    {
        $solicitud = $this->repository->findById($id);
        $nombreDocumento = $solicitud->nombre_documento; // Guardar antes de eliminar

        $deleted = $this->repository->delete($id);

        // Log de auditoría
        if ($deleted) {
            $this->auditLogger->logSolicitudDeleted($id, $nombreDocumento);
        }

        return $deleted;
    }
}
