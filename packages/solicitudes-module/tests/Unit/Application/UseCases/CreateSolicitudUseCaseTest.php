<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use SolicitudesModule\Application\DTOs\CreateSolicitudDTO;
use SolicitudesModule\Application\UseCases\CreateSolicitudUseCase;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;
use SolicitudesModule\Infrastructure\Services\NullAuditLogger;
use SolicitudesModule\Infrastructure\Services\InMemoryEventDispatcher;

final class CreateSolicitudUseCaseTest extends TestCase
{
    private SolicitudRepositoryInterface $repository;
    private CreateSolicitudUseCase $useCase;
    private InMemoryEventDispatcher $eventDispatcher;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SolicitudRepositoryInterface::class);
        $this->eventDispatcher = new InMemoryEventDispatcher();
        
        $this->useCase = new CreateSolicitudUseCase(
            $this->repository,
            new NullAuditLogger(),
            $this->eventDispatcher
        );
    }

    public function test_creates_solicitud_successfully(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (Solicitud $solicitud) {
                $solicitud->assignId(SolicitudId::fromInt(1));
            });

        $dto = new CreateSolicitudDTO(nombreDocumento: 'Test Document');
        $result = $this->useCase->execute($dto);

        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test Document', $result->nombreDocumento);
        $this->assertEquals('pendiente', $result->estado);
        $this->assertEquals('Pendiente', $result->estadoLabel);
    }

    public function test_dispatches_event_on_creation(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (Solicitud $solicitud) {
                $solicitud->assignId(SolicitudId::fromInt(1));
            });

        $dto = new CreateSolicitudDTO(nombreDocumento: 'Test Document');
        $this->useCase->execute($dto);

        $events = $this->eventDispatcher->getDispatchedEvents();
        $this->assertCount(1, $events);
        $this->assertEquals('solicitud.created', $events[0]->eventName());
    }

    public function test_throws_exception_for_invalid_nombre_documento(): void
    {
        $dto = new CreateSolicitudDTO(nombreDocumento: 'ab');

        $this->expectException(\SolicitudesModule\Domain\Exceptions\InvalidNombreDocumentoException::class);
        $this->useCase->execute($dto);
    }
}
