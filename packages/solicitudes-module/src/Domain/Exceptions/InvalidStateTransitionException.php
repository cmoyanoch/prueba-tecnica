<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Exceptions;

use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Excepción para transiciones de estado inválidas.
 */
final class InvalidStateTransitionException extends DomainException
{
    private const string ERROR_CODE = 'SOLICITUD_INVALID_STATE_TRANSITION';

    public static function cannotTransition(
        EstadoSolicitud $from,
        EstadoSolicitud $to
    ): self {
        return new self(
            sprintf(
                'No se puede cambiar el estado de "%s" a "%s"',
                $from->label(),
                $to->label()
            )
        );
    }

    public function errorCode(): string
    {
        return self::ERROR_CODE;
    }
}
