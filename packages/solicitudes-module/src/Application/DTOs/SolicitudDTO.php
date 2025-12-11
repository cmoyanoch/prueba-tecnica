<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\DTOs;

use SolicitudesModule\Domain\Entities\Solicitud;

/**
 * DTO de respuesta para una solicitud.
 * Representa los datos de una solicitud para la capa de presentación.
 */
final readonly class SolicitudDTO
{
    public function __construct(
        public int $id,
        public string $nombreDocumento,
        public string $estado,
        public string $estadoLabel,
        public string $estadoColor,
        public string $createdAt,
        public string $updatedAt,
        public bool $puedeSerAprobada,
        public bool $puedeSerRechazada,
        public bool $puedeSerModificada,
        public bool $puedeSerEliminada
    ) {}

    /**
     * Crea desde una entidad de dominio.
     */
    public static function fromEntity(Solicitud $solicitud): self
    {
        return new self(
            id: $solicitud->id()?->value() ?? 0,
            nombreDocumento: $solicitud->nombreDocumento()->value(),
            estado: $solicitud->estado()->value,
            estadoLabel: $solicitud->estado()->label(),
            estadoColor: $solicitud->estado()->color(),
            createdAt: $solicitud->createdAt()->format('Y-m-d H:i:s'),
            updatedAt: $solicitud->updatedAt()->format('Y-m-d H:i:s'),
            puedeSerAprobada: $solicitud->puedeSerAprobada(),
            puedeSerRechazada: $solicitud->puedeSerRechazada(),
            puedeSerModificada: $solicitud->puedeSerModificada(),
            puedeSerEliminada: $solicitud->puedeSerEliminada()
        );
    }

    /**
     * Crea desde un array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            nombreDocumento: $data['nombre_documento'] ?? '',
            estado: $data['estado'] ?? '',
            estadoLabel: $data['estado_label'] ?? '',
            estadoColor: $data['estado_color'] ?? '',
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? '',
            puedeSerAprobada: (bool) ($data['puede_ser_aprobada'] ?? false),
            puedeSerRechazada: (bool) ($data['puede_ser_rechazada'] ?? false),
            puedeSerModificada: (bool) ($data['puede_ser_modificada'] ?? false),
            puedeSerEliminada: (bool) ($data['puede_ser_eliminada'] ?? true)
        );
    }

    /**
     * Convierte a array para serialización JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre_documento' => $this->nombreDocumento,
            'estado' => $this->estado,
            'estado_label' => $this->estadoLabel,
            'estado_color' => $this->estadoColor,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'puede_ser_aprobada' => $this->puedeSerAprobada,
            'puede_ser_rechazada' => $this->puedeSerRechazada,
            'puede_ser_modificada' => $this->puedeSerModificada,
            'puede_ser_eliminada' => $this->puedeSerEliminada,
        ];
    }
}
