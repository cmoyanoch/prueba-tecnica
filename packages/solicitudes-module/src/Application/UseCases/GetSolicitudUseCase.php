<?php

declare(strict_types=1);

namespace SolicitudesModule\Application\UseCases;

use SolicitudesModule\Application\DTOs\SolicitudDTO;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Caso de uso: Obtener una solicitud por ID.
 */
final readonly class GetSolicitudUseCase
{
    public function __construct(
        private SolicitudRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta el caso de uso.
     *
     * @throws \SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException
     * @throws \SolicitudesModule\Domain\Exceptions\InvalidSolicitudIdException
     */
    public function execute(int $id): SolicitudDTO
    {
        $solicitudId = SolicitudId::fromInt($id);
        $solicitud = $this->repository->findByIdOrFail($solicitudId);

        return SolicitudDTO::fromEntity($solicitud);
    }

    /**
     * Ejecuta el caso de uso retornando null si no existe.
     */
    public function executeOrNull(int $id): ?SolicitudDTO
    {
        try {
            $solicitudId = SolicitudId::fromInt($id);
            $solicitud = $this->repository->findById($solicitudId);

            return $solicitud ? SolicitudDTO::fromEntity($solicitud) : null;
        } catch (\Exception) {
            return null;
        }
    }
}
