<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Domain\ValueObjects;

use App\Modules\Solicitudes\Domain\Exceptions\InvalidSolicitudIdException;

/**
 * Value Object para el identificador de Solicitud.
 * Inmutable y auto-validante.
 */
final readonly class SolicitudId
{
    private function __construct(
        private int $value
    ) {}

    /**
     * Crea una instancia desde un entero.
     *
     * @throws InvalidSolicitudIdException
     */
    public static function fromInt(int $value): self
    {
        if ($value <= 0) {
            throw InvalidSolicitudIdException::invalidId($value);
        }

        return new self($value);
    }

    /**
     * Crea una instancia desde un string.
     *
     * @throws InvalidSolicitudIdException
     */
    public static function fromString(string $value): self
    {
        if (!is_numeric($value)) {
            throw InvalidSolicitudIdException::notNumeric($value);
        }

        return self::fromInt((int) $value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
