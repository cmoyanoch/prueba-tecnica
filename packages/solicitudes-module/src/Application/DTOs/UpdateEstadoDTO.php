<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\DTOs;

use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * DTO para actualizar el estado de una solicitud.
 */
final readonly class UpdateEstadoDTO
{
    public function __construct(
        public int $solicitudId,
        public EstadoSolicitud $estado
    ) {}

    /**
     * Crea desde un array asociativo.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            solicitudId: (int) ($data['solicitud_id'] ?? $data['id'] ?? 0),
            estado: is_string($data['estado'] ?? null)
                ? EstadoSolicitud::from($data['estado'])
                : $data['estado']
        );
    }
}
