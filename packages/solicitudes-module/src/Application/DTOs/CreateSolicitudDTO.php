<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\DTOs;

/**
 * DTO para crear una solicitud.
 * Inmutable y auto-validante.
 */
final readonly class CreateSolicitudDTO
{
    public function __construct(
        public string $nombreDocumento
    ) {}

    /**
     * Crea desde un array asociativo.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            nombreDocumento: $data['nombre_documento'] ?? ''
        );
    }

    /**
     * Convierte a array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'nombre_documento' => $this->nombreDocumento,
        ];
    }
}
