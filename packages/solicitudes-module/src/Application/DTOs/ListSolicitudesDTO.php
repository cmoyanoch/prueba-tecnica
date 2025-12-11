<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\DTOs;

/**
 * DTO para listar solicitudes con filtros y paginación.
 */
final readonly class ListSolicitudesDTO
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $estado = null,
        public ?string $search = null,
        public string $sortBy = 'id',
        public string $sortOrder = 'desc'
    ) {}

    /**
     * Crea desde un array asociativo (típicamente query params).
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int) ($data['page'] ?? 1)),
            perPage: min(100, max(1, (int) ($data['per_page'] ?? 15))),
            estado: isset($data['estado']) && $data['estado'] !== '' ? (string) $data['estado'] : null,
            search: isset($data['search']) && $data['search'] !== '' ? (string) $data['search'] : null,
            sortBy: in_array($data['sort_by'] ?? 'id', ['id', 'nombre_documento', 'estado', 'created_at'], true)
                ? $data['sort_by']
                : 'id',
            sortOrder: in_array(strtolower($data['sort_order'] ?? 'desc'), ['asc', 'desc'], true)
                ? strtolower($data['sort_order'])
                : 'desc'
        );
    }
}
