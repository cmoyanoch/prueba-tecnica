<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Tests\Unit;

use App\Modules\Solicitudes\Application\Actions\ListSolicitudesAction;
use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ListSolicitudesActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_delega_al_repository_y_retorna_coleccion(): void
    {
        // Arrange
        $expectedCollection = Collection::make([
            (object) ['id' => 1, 'nombre_documento' => 'Test 1'],
            (object) ['id' => 2, 'nombre_documento' => 'Test 2'],
        ]);

        $repository = Mockery::mock(SolicitudRepositoryInterface::class);
        $repository->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedCollection);

        $auditLogger = Mockery::mock(AuditLoggerInterface::class);
        $auditLogger->shouldReceive('logSolicitudesListed')
            ->once()
            ->with(2);

        $action = new ListSolicitudesAction($repository, $auditLogger);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($expectedCollection, $result);
    }

    public function test_execute_retorna_coleccion_vacia_cuando_no_hay_solicitudes(): void
    {
        // Arrange
        $emptyCollection = Collection::make([]);

        $repository = Mockery::mock(SolicitudRepositoryInterface::class);
        $repository->shouldReceive('getAll')
            ->once()
            ->andReturn($emptyCollection);

        $auditLogger = Mockery::mock(AuditLoggerInterface::class);
        $auditLogger->shouldReceive('logSolicitudesListed')
            ->once()
            ->with(0);

        $action = new ListSolicitudesAction($repository, $auditLogger);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
