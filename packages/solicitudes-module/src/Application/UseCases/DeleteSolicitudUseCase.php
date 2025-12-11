<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\UseCases;

use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Events\SolicitudDeletedEvent;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Caso de uso: Eliminar una solicitud.
 */
final readonly class DeleteSolicitudUseCase
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger,
        private ?EventDispatcherInterface $eventDispatcher = null
    ) {}

    /**
     * Ejecuta el caso de uso.
     *
     * @throws \SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException
     */
    public function execute(int $id): bool
    {
        $solicitudId = SolicitudId::fromInt($id);
        $solicitud = $this->repository->findByIdOrFail($solicitudId);

        if (!$solicitud->puedeSerEliminada()) {
            return false;
        }

        $nombreDocumento = $solicitud->nombreDocumento()->value();

        // Eliminar
        $this->repository->delete($solicitud);

        // Log de auditoría
        $this->auditLogger->logSolicitudDeleted($id, $nombreDocumento);

        // Emitir evento de dominio
        $this->eventDispatcher?->dispatch(
            new SolicitudDeletedEvent($id, $nombreDocumento)
        );

        return true;
    }

    /**
     * Elimina sin verificar permisos.
     * Uso administrativo.
     */
    public function forceExecute(int $id): bool
    {
        $solicitudId = SolicitudId::fromInt($id);
        $solicitud = $this->repository->findById($solicitudId);

        if ($solicitud === null) {
            return false;
        }

        $nombreDocumento = $solicitud->nombreDocumento()->value();

        $this->repository->delete($solicitud);
        $this->auditLogger->logSolicitudDeleted($id, $nombreDocumento);

        $this->eventDispatcher?->dispatch(
            new SolicitudDeletedEvent($id, $nombreDocumento)
        );

        return true;
    }
}
