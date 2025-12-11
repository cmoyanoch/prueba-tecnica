<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Enums;

/**
 * Estados posibles de una solicitud.
 * PHP puro - Sin dependencias de framework.
 */
enum EstadoSolicitud: string
{
    case PENDIENTE = 'pendiente';
    case APROBADO = 'aprobado';
    case RECHAZADO = 'rechazado';
    case MODIFICAR = 'modificar';

    /**
     * Obtiene todos los valores posibles del enum.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtiene la etiqueta legible para el estado.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::APROBADO => 'Aprobado',
            self::RECHAZADO => 'Rechazado',
            self::MODIFICAR => 'Modificar',
        };
    }

    /**
     * Obtiene el color asociado al estado para la UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDIENTE => 'warning',
            self::APROBADO => 'success',
            self::RECHAZADO => 'danger',
            self::MODIFICAR => 'info',
        };
    }

    /**
     * Verifica si la transición a otro estado es válida.
     */
    public function canTransitionTo(self $newState): bool
    {
        return match ($this) {
            self::PENDIENTE => in_array($newState, [self::APROBADO, self::RECHAZADO, self::MODIFICAR], true),
            self::MODIFICAR => in_array($newState, [self::APROBADO, self::RECHAZADO, self::PENDIENTE], true),
            self::APROBADO, self::RECHAZADO => $newState === self::MODIFICAR,
        };
    }
}
