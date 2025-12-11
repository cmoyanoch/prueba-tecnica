<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Exceptions;

/**
 * Excepción para IDs de solicitud inválidos.
 */
final class InvalidSolicitudIdException extends DomainException
{
    private const string ERROR_CODE = 'SOLICITUD_INVALID_ID';

    public static function invalidId(int $value): self
    {
        return new self(
            sprintf('El ID de solicitud debe ser mayor a 0, se recibió: %d', $value)
        );
    }

    public static function notNumeric(string $value): self
    {
        return new self(
            sprintf('El ID de solicitud debe ser numérico, se recibió: "%s"', $value)
        );
    }

    public function errorCode(): string
    {
        return self::ERROR_CODE;
    }
}
