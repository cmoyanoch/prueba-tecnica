<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use SolicitudesModule\Application\DTOs\UpdateEstadoDTO;
use SolicitudesModule\Application\UseCases\UpdateEstadoSolicitudUseCase;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\InvalidStateTransitionException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;
use SolicitudesModule\Infrastructure\Services\NullAuditLogger;
use SolicitudesModule\Infrastructure\Services\InMemoryEventDispatcher;

final class UpdateEstadoSolicitudUseCaseTest extends TestCase
{
    private SolicitudRepositoryInterface $repository;
    private UpdateEstadoSolicitudUseCase $useCase;
    private InMemoryEventDispatcher $eventDispatcher;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SolicitudRepositoryInterface::class);
        $this->eventDispatcher = new InMemoryEventDispatcher();
        
        $this->useCase = new UpdateEstadoSolicitudUseCase(
            $this->repository,
            new NullAuditLogger(),
            $this->eventDispatcher
        );
    }

    public function test_updates_estado_successfully(): void
    {
        $solicitud = $this->createSolicitud(EstadoSolicitud::PENDIENTE);
        
        $this->repository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->willReturn($solicitud);

        $this->repository
            ->expects($this->once())
            ->method('save');

        $dto = new UpdateEstadoDTO(
            solicitudId: 1,
            estado: EstadoSolicitud::APROBADO
        );

        $result = $this->useCase->execute($dto);

        $this->assertEquals('aprobado', $result->estado);
    }

    public function test_throws_exception_for_invalid_transition(): void
    {
        $solicitud = $this->createSolicitud(EstadoSolicitud::APROBADO);
        
        $this->repository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->willReturn($solicitud);

        $dto = new UpdateEstadoDTO(
            solicitudId: 1,
            estado: EstadoSolicitud::RECHAZADO
        );

        $this->expectException(InvalidStateTransitionException::class);
        $this->useCase->execute($dto);
    }

    public function test_force_execute_bypasses_transition_validation(): void
    {
        $solicitud = $this->createSolicitud(EstadoSolicitud::APROBADO);
        
        $this->repository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->willReturn($solicitud);

        $this->repository
            ->expects($this->once())
            ->method('save');

        $result = $this->useCase->forceExecute(1, EstadoSolicitud::RECHAZADO);

        $this->assertEquals('rechazado', $result->estado);
    }

    public function test_dispatches_event_on_estado_change(): void
    {
        $solicitud = $this->createSolicitud(EstadoSolicitud::PENDIENTE);
        
        $this->repository
            ->method('findByIdOrFail')
            ->willReturn($solicitud);

        $dto = new UpdateEstadoDTO(
            solicitudId: 1,
            estado: EstadoSolicitud::APROBADO
        );

        $this->useCase->execute($dto);

        $events = $this->eventDispatcher->getDispatchedEvents();
        $this->assertCount(1, $events);
        $this->assertEquals('solicitud.estado_changed', $events[0]->eventName());
    }

    private function createSolicitud(EstadoSolicitud $estado): Solicitud
    {
        $solicitud = Solicitud::create(
            NombreDocumento::fromString('Test Document'),
            $estado
        );
        $solicitud->assignId(SolicitudId::fromInt(1));
        
        return $solicitud;
    }
}
