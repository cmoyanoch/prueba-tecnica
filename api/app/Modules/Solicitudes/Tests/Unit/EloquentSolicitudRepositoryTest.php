<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Tests\Unit;

use App\Modules\Solicitudes\Domain\Entities\Solicitud;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use App\Modules\Solicitudes\Infrastructure\Repositories\EloquentSolicitudRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

final class EloquentSolicitudRepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_getAll_retorna_coleccion_ordenada_por_created_at_desc(): void
    {
        // Arrange
        $collection = Collection::make([
            (object) ['id' => 2, 'created_at' => '2024-01-02'],
            (object) ['id' => 1, 'created_at' => '2024-01-01'],
        ]);

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('orderByDesc')
            ->once()
            ->with('created_at')
            ->andReturnSelf();
        $query->shouldReceive('get')
            ->once()
            ->andReturn($collection);

        $model = Mockery::mock(Solicitud::class);
        $model->shouldReceive('newQuery')
            ->once()
            ->andReturn($query);

        $repository = new EloquentSolicitudRepository($model);

        // Act
        $result = $repository->getAll();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_findById_retorna_solicitud_cuando_existe(): void
    {
        // Arrange
        $id = 1;
        $solicitud = new Solicitud();
        $solicitud->id = $id;
        $solicitud->nombre_documento = 'Test';

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($solicitud);

        $model = Mockery::mock(Solicitud::class);
        $model->shouldReceive('newQuery')
            ->once()
            ->andReturn($query);

        $repository = new EloquentSolicitudRepository($model);

        // Act
        $result = $repository->findById($id);

        // Assert
        $this->assertInstanceOf(Solicitud::class, $result);
        $this->assertEquals($id, $result->id);
    }

    public function test_create_crea_nueva_solicitud(): void
    {
        // Arrange
        $data = [
            'nombre_documento' => 'Nuevo Documento',
            'estado' => EstadoSolicitud::PENDIENTE->value,
        ];

        $solicitud = new Solicitud();
        $solicitud->id = 1;
        $solicitud->nombre_documento = $data['nombre_documento'];
        $solicitud->estado = EstadoSolicitud::PENDIENTE;

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($solicitud);

        $model = Mockery::mock(Solicitud::class);
        $model->shouldReceive('newQuery')
            ->once()
            ->andReturn($query);

        $repository = new EloquentSolicitudRepository($model);

        // Act
        $result = $repository->create($data);

        // Assert
        $this->assertInstanceOf(Solicitud::class, $result);
        $this->assertEquals($data['nombre_documento'], $result->nombre_documento);
    }
}
