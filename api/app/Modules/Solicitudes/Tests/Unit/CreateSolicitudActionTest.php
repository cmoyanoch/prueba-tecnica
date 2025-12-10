<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Tests\Unit;

use App\Modules\Solicitudes\Application\Actions\CreateSolicitudAction;
use App\Modules\Solicitudes\Application\DTOs\CreateSolicitudDTO;
use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Mockery;
use PHPUnit\Framework\TestCase;

final class CreateSolicitudActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_crea_solicitud_con_estado_pendiente(): void
    {
        // Arrange
        $dto = new CreateSolicitudDTO('Contrato de Prueba');
        $expectedSolicitud = (object) [
            'id' => 1,
            'nombre_documento' => 'Contrato de Prueba',
            'estado' => EstadoSolicitud::PENDIENTE->value,
        ];

        $repository = Mockery::mock(SolicitudRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'nombre_documento' => 'Contrato de Prueba',
                'estado' => EstadoSolicitud::PENDIENTE->value,
            ])
            ->andReturn($expectedSolicitud);

        $auditLogger = Mockery::mock(AuditLoggerInterface::class);
        $auditLogger->shouldReceive('logSolicitudCreated')
            ->once()
            ->with(1, 'Contrato de Prueba');

        $action = new CreateSolicitudAction($repository, $auditLogger);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals($expectedSolicitud, $result);
        $this->assertEquals('Contrato de Prueba', $result->nombre_documento);
        $this->assertEquals(EstadoSolicitud::PENDIENTE->value, $result->estado);
    }
}
