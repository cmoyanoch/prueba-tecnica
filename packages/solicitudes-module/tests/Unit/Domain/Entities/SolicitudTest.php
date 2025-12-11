<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Domain\Entities;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\InvalidStateTransitionException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

final class SolicitudTest extends TestCase
{
    public function test_can_create_solicitud(): void
    {
        $nombreDocumento = NombreDocumento::fromString('Test Document');
        $solicitud = Solicitud::create($nombreDocumento);

        $this->assertNull($solicitud->id());
        $this->assertEquals('Test Document', $solicitud->nombreDocumento()->value());
        $this->assertEquals(EstadoSolicitud::PENDIENTE, $solicitud->estado());
        $this->assertInstanceOf(DateTimeImmutable::class, $solicitud->createdAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $solicitud->updatedAt());
    }

    public function test_can_reconstitute_solicitud(): void
    {
        $id = SolicitudId::fromInt(1);
        $nombreDocumento = NombreDocumento::fromString('Test Document');
        $estado = EstadoSolicitud::APROBADO;
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-01 11:00:00');

        $solicitud = Solicitud::reconstitute(
            $id,
            $nombreDocumento,
            $estado,
            $createdAt,
            $updatedAt
        );

        $this->assertEquals(1, $solicitud->id()->value());
        $this->assertEquals('Test Document', $solicitud->nombreDocumento()->value());
        $this->assertEquals(EstadoSolicitud::APROBADO, $solicitud->estado());
        $this->assertEquals($createdAt, $solicitud->createdAt());
        $this->assertEquals($updatedAt, $solicitud->updatedAt());
    }

    public function test_can_change_estado_from_pendiente_to_aprobado(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));

        $solicitud->cambiarEstado(EstadoSolicitud::APROBADO);

        $this->assertEquals(EstadoSolicitud::APROBADO, $solicitud->estado());
    }

    public function test_can_change_estado_from_pendiente_to_rechazado(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));

        $solicitud->cambiarEstado(EstadoSolicitud::RECHAZADO);

        $this->assertEquals(EstadoSolicitud::RECHAZADO, $solicitud->estado());
    }

    public function test_cannot_change_estado_from_aprobado_to_rechazado(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));
        $solicitud->cambiarEstado(EstadoSolicitud::APROBADO);

        $this->expectException(InvalidStateTransitionException::class);
        $solicitud->cambiarEstado(EstadoSolicitud::RECHAZADO);
    }

    public function test_can_change_estado_from_aprobado_to_modificar(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));
        $solicitud->cambiarEstado(EstadoSolicitud::APROBADO);

        $solicitud->cambiarEstado(EstadoSolicitud::MODIFICAR);

        $this->assertEquals(EstadoSolicitud::MODIFICAR, $solicitud->estado());
    }

    public function test_puede_ser_aprobada_returns_true_when_pendiente(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));

        $this->assertTrue($solicitud->puedeSerAprobada());
    }

    public function test_puede_ser_aprobada_returns_false_when_aprobado(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));
        $solicitud->cambiarEstado(EstadoSolicitud::APROBADO);

        $this->assertFalse($solicitud->puedeSerAprobada());
    }

    public function test_puede_ser_modificada_returns_true_when_aprobado(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));
        $solicitud->cambiarEstado(EstadoSolicitud::APROBADO);

        $this->assertTrue($solicitud->puedeSerModificada());
    }

    public function test_puede_ser_modificada_returns_false_when_pendiente(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));

        $this->assertFalse($solicitud->puedeSerModificada());
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test Document'));
        $solicitud->assignId(SolicitudId::fromInt(1));

        $array = $solicitud->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('nombre_documento', $array);
        $this->assertArrayHasKey('estado', $array);
        $this->assertArrayHasKey('estado_label', $array);
        $this->assertArrayHasKey('estado_color', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
        
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Test Document', $array['nombre_documento']);
        $this->assertEquals('pendiente', $array['estado']);
    }

    public function test_assign_id_throws_exception_if_already_assigned(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));
        $solicitud->assignId(SolicitudId::fromInt(1));

        $this->expectException(\LogicException::class);
        $solicitud->assignId(SolicitudId::fromInt(2));
    }

    public function test_forzar_estado_bypasses_validation(): void
    {
        $solicitud = Solicitud::create(NombreDocumento::fromString('Test'));
        $solicitud->cambiarEstado(EstadoSolicitud::APROBADO);

        // Normalmente esto fallaría con cambiarEstado
        $solicitud->forzarEstado(EstadoSolicitud::RECHAZADO);

        $this->assertEquals(EstadoSolicitud::RECHAZADO, $solicitud->estado());
    }
}
