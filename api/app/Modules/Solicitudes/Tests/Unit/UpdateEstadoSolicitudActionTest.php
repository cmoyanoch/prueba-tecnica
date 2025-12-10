<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Tests\Unit;

use App\Modules\Solicitudes\Application\Actions\UpdateEstadoSolicitudAction;
use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Mockery;
use PHPUnit\Framework\TestCase;

final class UpdateEstadoSolicitudActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_actualiza_estado_a_aprobado(): void
    {
        // Arrange
        $id = 1;
        $estado = EstadoSolicitud::APROBADO;
        $expectedSolicitud = (object) [
            'id' => $id,
            'nombre_documento' => 'Test',
            'estado' => EstadoSolicitud::APROBADO->value,
        ];

        $solicitudAnterior = (object) [
            'id' => $id,
            'estado' => (object) ['value' => EstadoSolicitud::PENDIENTE->value],
        ];

        $repository = Mockery::mock(SolicitudRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($solicitudAnterior);
        $repository->shouldReceive('updateEstado')
            ->once()
            ->with($id, $estado)
            ->andReturn($expectedSolicitud);

        $auditLogger = Mockery::mock(AuditLoggerInterface::class);
        $auditLogger->shouldReceive('logEstadoUpdated')
            ->once()
            ->with($id, EstadoSolicitud::PENDIENTE->value, EstadoSolicitud::APROBADO->value);

        $action = new UpdateEstadoSolicitudAction($repository, $auditLogger);

        // Act
        $result = $action->execute($id, $estado);

        // Assert
        $this->assertEquals($expectedSolicitud, $result);
        $this->assertEquals(EstadoSolicitud::APROBADO->value, $result->estado);
    }
}
