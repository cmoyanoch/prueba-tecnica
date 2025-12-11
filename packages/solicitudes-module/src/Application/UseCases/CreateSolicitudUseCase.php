<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\UseCases;

use SolicitudesModule\Application\DTOs\CreateSolicitudDTO;
use SolicitudesModule\Application\DTOs\SolicitudDTO;
use SolicitudesModule\Domain\Contracts\AuditLoggerInterface;
use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Events\SolicitudCreatedEvent;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;

/**
 * Caso de uso: Crear una nueva solicitud.
 */
final readonly class CreateSolicitudUseCase
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger,
        private ?EventDispatcherInterface $eventDispatcher = null
    ) {}

    /**
     * Ejecuta el caso de uso.
     *
     * @throws \SolicitudesModule\Domain\Exceptions\InvalidNombreDocumentoException
     */
    public function execute(CreateSolicitudDTO $dto): SolicitudDTO
    {
        // Crear Value Object (valida automáticamente)
        $nombreDocumento = NombreDocumento::fromString($dto->nombreDocumento);

        // Crear entidad de dominio
        $solicitud = Solicitud::create($nombreDocumento);

        // Persistir
        $this->repository->save($solicitud);

        // Log de auditoría
        $this->auditLogger->logSolicitudCreated(
            $solicitud->id()->value(),
            $solicitud->nombreDocumento()->value()
        );

        // Emitir evento de dominio
        $this->eventDispatcher?->dispatch(
            SolicitudCreatedEvent::fromEntity($solicitud)
        );

        return SolicitudDTO::fromEntity($solicitud);
    }
}
