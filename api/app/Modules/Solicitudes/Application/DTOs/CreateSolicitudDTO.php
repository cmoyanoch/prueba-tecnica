<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\DTOs;

final readonly class CreateSolicitudDTO
{
    public function __construct(
        public string $nombreDocumento,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombreDocumento: $data['nombre_documento'],
        );
    }
}
