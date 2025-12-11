<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\UseCases;

use SolicitudesModule\Application\DTOs\ListSolicitudesDTO;
use SolicitudesModule\Application\DTOs\SolicitudDTO;
use SolicitudesModule\Domain\Contracts\PaginatedResult;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Caso de uso: Listar solicitudes con paginación y filtros.
 */
final readonly class ListSolicitudesUseCase
{
    public function __construct(
        private SolicitudRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta el caso de uso con paginación.
     *
     * @return PaginatedResult<SolicitudDTO>
     */
    public function execute(ListSolicitudesDTO $dto): PaginatedResult
    {
        $result = $this->repository->findAllPaginated($dto->page, $dto->perPage);

        $dtos = array_map(
            fn($solicitud) => SolicitudDTO::fromEntity($solicitud),
            $result->items()
        );

        return PaginatedResult::create(
            items: $dtos,
            total: $result->total(),
            perPage: $result->perPage(),
            currentPage: $result->currentPage()
        );
    }

    /**
     * Ejecuta el caso de uso sin paginación.
     *
     * @return array<SolicitudDTO>
     */
    public function executeAll(): array
    {
        $solicitudes = $this->repository->findAll();

        return array_map(
            fn($solicitud) => SolicitudDTO::fromEntity($solicitud),
            $solicitudes
        );
    }

    /**
     * Ejecuta el caso de uso filtrando por estado.
     *
     * @return array<SolicitudDTO>
     */
    public function executeByEstado(EstadoSolicitud $estado): array
    {
        $solicitudes = $this->repository->findByEstado($estado);

        return array_map(
            fn($solicitud) => SolicitudDTO::fromEntity($solicitud),
            $solicitudes
        );
    }
}
