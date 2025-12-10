<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Domain\Enums;

enum EstadoSolicitud: string
{
    case PENDIENTE = 'pendiente';
    case APROBADO = 'aprobado';
    case RECHAZADO = 'rechazado';
    case MODIFICAR = 'modificar';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDIENTE => 'Pendiente',
            self::APROBADO => 'Aprobado',
            self::RECHAZADO => 'Rechazado',
            self::MODIFICAR => 'Modificar',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDIENTE => 'warning',
            self::APROBADO => 'success',
            self::RECHAZADO => 'danger',
            self::MODIFICAR => 'info',
        };
    }
}
