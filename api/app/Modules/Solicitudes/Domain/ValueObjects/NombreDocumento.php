<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Domain\ValueObjects;

use App\Modules\Solicitudes\Domain\Exceptions\InvalidNombreDocumentoException;

/**
 * Value Object para el nombre del documento.
 * Inmutable y auto-validante.
 */
final readonly class NombreDocumento
{
    private const int MIN_LENGTH = 3;
    private const int MAX_LENGTH = 255;

    private function __construct(
        private string $value
    ) {}

    /**
     * Crea una instancia desde un string.
     *
     * @throws InvalidNombreDocumentoException
     */
    public static function fromString(string $value): self
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw InvalidNombreDocumentoException::empty();
        }

        $length = mb_strlen($trimmed);

        if ($length < self::MIN_LENGTH) {
            throw InvalidNombreDocumentoException::tooShort($trimmed, self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidNombreDocumentoException::tooLong($trimmed, self::MAX_LENGTH);
        }

        return new self($trimmed);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
