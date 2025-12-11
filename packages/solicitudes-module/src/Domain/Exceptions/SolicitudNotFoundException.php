<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Exceptions;

use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Excepción cuando no se encuentra una solicitud.
 */
final class SolicitudNotFoundException extends DomainException
{
    private const string ERROR_CODE = 'SOLICITUD_NOT_FOUND';

    public static function withId(SolicitudId $id): self
    {
        return new self(
            sprintf('No se encontró la solicitud con ID: %s', $id)
        );
    }

    public static function withIdInt(int $id): self
    {
        return new self(
            sprintf('No se encontró la solicitud con ID: %d', $id)
        );
    }

    public function errorCode(): string
    {
        return self::ERROR_CODE;
    }
}
