<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Exceptions;

/**
 * Excepción para nombres de documento inválidos.
 */
final class InvalidNombreDocumentoException extends DomainException
{
    private const string ERROR_CODE = 'SOLICITUD_INVALID_NOMBRE_DOCUMENTO';

    public static function empty(): self
    {
        return new self('El nombre del documento no puede estar vacío');
    }

    public static function tooShort(string $value, int $minLength): self
    {
        return new self(
            sprintf(
                'El nombre del documento "%s" es demasiado corto. Mínimo %d caracteres',
                $value,
                $minLength
            )
        );
    }

    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(
            sprintf(
                'El nombre del documento es demasiado largo. Máximo %d caracteres',
                $maxLength
            )
        );
    }

    public function errorCode(): string
    {
        return self::ERROR_CODE;
    }
}
