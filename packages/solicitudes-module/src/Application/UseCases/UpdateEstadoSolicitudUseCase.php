<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\UseCases;

use SolicitudesModule\Application\DTOs\SolicitudDTO;
use SolicitudesModule\Application\DTOs\UpdateEstadoDTO;
use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Events\SolicitudEstadoChangedEvent;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Caso de uso: Actualizar el estado de una solicitud.
 */
final readonly class UpdateEstadoSolicitudUseCase
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger,
        private ?EventDispatcherInterface $eventDispatcher = null
    ) {}

    /**
     * Ejecuta el caso de uso desde DTO.
     *
     * @throws \SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException
     * @throws \SolicitudesModule\Domain\Exceptions\InvalidStateTransitionException
     */
    public function execute(UpdateEstadoDTO $dto): SolicitudDTO
    {
        return $this->executeWithParams($dto->solicitudId, $dto->estado);
    }

    /**
     * Ejecuta el caso de uso con parámetros directos.
     *
     * @throws \SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException
     * @throws \SolicitudesModule\Domain\Exceptions\InvalidStateTransitionException
     */
    public function executeWithParams(int $id, EstadoSolicitud $nuevoEstado): SolicitudDTO
    {
        $solicitudId = SolicitudId::fromInt($id);
        $solicitud = $this->repository->findByIdOrFail($solicitudId);

        $estadoAnterior = $solicitud->estado();

        // Cambiar estado (valida transiciones)
        $solicitud->cambiarEstado($nuevoEstado);

        // Persistir cambios
        $this->repository->save($solicitud);

        // Log de auditoría
        $this->auditLogger->logEstadoChanged(
            $solicitud->id()->value(),
            $estadoAnterior,
            $nuevoEstado
        );

        // Emitir evento de dominio
        $this->eventDispatcher?->dispatch(
            SolicitudEstadoChangedEvent::create(
                $solicitud->id()->value(),
                $estadoAnterior,
                $nuevoEstado
            )
        );

        return SolicitudDTO::fromEntity($solicitud);
    }

    /**
     * Fuerza el cambio de estado sin validar transiciones.
     * Uso administrativo.
     */
    public function forceExecute(int $id, EstadoSolicitud $nuevoEstado): SolicitudDTO
    {
        $solicitudId = SolicitudId::fromInt($id);
        $solicitud = $this->repository->findByIdOrFail($solicitudId);

        $estadoAnterior = $solicitud->estado();
        $solicitud->forzarEstado($nuevoEstado);

        $this->repository->save($solicitud);

        $this->auditLogger->logEstadoChanged(
            $solicitud->id()->value(),
            $estadoAnterior,
            $nuevoEstado
        );

        $this->eventDispatcher?->dispatch(
            SolicitudEstadoChangedEvent::create(
                $solicitud->id()->value(),
                $estadoAnterior,
                $nuevoEstado
            )
        );

        return SolicitudDTO::fromEntity($solicitud);
    }
}
