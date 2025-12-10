<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\Actions;

use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class ListSolicitudesAction
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger
    ) {}

    public function execute(): Collection
    {
        $solicitudes = $this->repository->getAll();

        // Log de auditoría
        $this->auditLogger->logSolicitudesListed($solicitudes->count());

        return $solicitudes;
    }

    public function executePaginated(int $perPage = 5): LengthAwarePaginator
    {
        $paginator = $this->repository->getAllPaginated($perPage);

        // Log de auditoría
        $this->auditLogger->logSolicitudesListed($paginator->count());

        return $paginator;
    }
}
