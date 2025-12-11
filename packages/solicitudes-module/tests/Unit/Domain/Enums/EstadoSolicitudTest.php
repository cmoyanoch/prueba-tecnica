<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Domain\Enums;

use PHPUnit\Framework\TestCase;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

final class EstadoSolicitudTest extends TestCase
{
    public function test_values_returns_all_values(): void
    {
        $values = EstadoSolicitud::values();

        $this->assertCount(4, $values);
        $this->assertContains('pendiente', $values);
        $this->assertContains('aprobado', $values);
        $this->assertContains('rechazado', $values);
        $this->assertContains('modificar', $values);
    }

    public function test_label_returns_correct_labels(): void
    {
        $this->assertEquals('Pendiente', EstadoSolicitud::PENDIENTE->label());
        $this->assertEquals('Aprobado', EstadoSolicitud::APROBADO->label());
        $this->assertEquals('Rechazado', EstadoSolicitud::RECHAZADO->label());
        $this->assertEquals('Modificar', EstadoSolicitud::MODIFICAR->label());
    }

    public function test_color_returns_correct_colors(): void
    {
        $this->assertEquals('warning', EstadoSolicitud::PENDIENTE->color());
        $this->assertEquals('success', EstadoSolicitud::APROBADO->color());
        $this->assertEquals('danger', EstadoSolicitud::RECHAZADO->color());
        $this->assertEquals('info', EstadoSolicitud::MODIFICAR->color());
    }

    /**
     * @dataProvider validTransitionsProvider
     */
    public function test_can_transition_to_valid_states(
        EstadoSolicitud $from,
        EstadoSolicitud $to
    ): void {
        $this->assertTrue($from->canTransitionTo($to));
    }

    /**
     * @dataProvider invalidTransitionsProvider
     */
    public function test_cannot_transition_to_invalid_states(
        EstadoSolicitud $from,
        EstadoSolicitud $to
    ): void {
        $this->assertFalse($from->canTransitionTo($to));
    }

    public static function validTransitionsProvider(): array
    {
        return [
            'pendiente -> aprobado' => [EstadoSolicitud::PENDIENTE, EstadoSolicitud::APROBADO],
            'pendiente -> rechazado' => [EstadoSolicitud::PENDIENTE, EstadoSolicitud::RECHAZADO],
            'pendiente -> modificar' => [EstadoSolicitud::PENDIENTE, EstadoSolicitud::MODIFICAR],
            'modificar -> aprobado' => [EstadoSolicitud::MODIFICAR, EstadoSolicitud::APROBADO],
            'modificar -> rechazado' => [EstadoSolicitud::MODIFICAR, EstadoSolicitud::RECHAZADO],
            'aprobado -> modificar' => [EstadoSolicitud::APROBADO, EstadoSolicitud::MODIFICAR],
            'rechazado -> modificar' => [EstadoSolicitud::RECHAZADO, EstadoSolicitud::MODIFICAR],
        ];
    }

    public static function invalidTransitionsProvider(): array
    {
        return [
            'aprobado -> rechazado' => [EstadoSolicitud::APROBADO, EstadoSolicitud::RECHAZADO],
            'aprobado -> pendiente' => [EstadoSolicitud::APROBADO, EstadoSolicitud::PENDIENTE],
            'rechazado -> aprobado' => [EstadoSolicitud::RECHAZADO, EstadoSolicitud::APROBADO],
            'rechazado -> pendiente' => [EstadoSolicitud::RECHAZADO, EstadoSolicitud::PENDIENTE],
        ];
    }
}
