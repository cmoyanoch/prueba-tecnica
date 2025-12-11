<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Contracts;

use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Contrato para el repositorio de Solicitudes.
 * Define las operaciones de persistencia sin acoplar a ningún ORM/Framework.
 */
interface SolicitudRepositoryInterface
{
    /**
     * Guarda una solicitud (crear o actualizar).
     */
    public function save(Solicitud $solicitud): void;

    /**
     * Busca una solicitud por ID.
     */
    public function findById(SolicitudId $id): ?Solicitud;

    /**
     * Busca una solicitud por ID o lanza excepción.
     *
     * @throws \SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException
     */
    public function findByIdOrFail(SolicitudId $id): Solicitud;

    /**
     * Obtiene todas las solicitudes.
     *
     * @return array<Solicitud>
     */
    public function findAll(): array;

    /**
     * Obtiene solicitudes paginadas.
     *
     * @return PaginatedResult<Solicitud>
     */
    public function findAllPaginated(int $page = 1, int $perPage = 15): PaginatedResult;

    /**
     * Busca solicitudes por estado.
     *
     * @return array<Solicitud>
     */
    public function findByEstado(EstadoSolicitud $estado): array;

    /**
     * Elimina una solicitud.
     */
    public function delete(Solicitud $solicitud): void;

    /**
     * Elimina una solicitud por ID.
     */
    public function deleteById(SolicitudId $id): bool;

    /**
     * Cuenta el total de solicitudes.
     */
    public function count(): int;

    /**
     * Cuenta solicitudes por estado.
     */
    public function countByEstado(EstadoSolicitud $estado): int;

    /**
     * Verifica si existe una solicitud con el ID dado.
     */
    public function exists(SolicitudId $id): bool;

    /**
     * Obtiene el siguiente ID disponible (para algunos motores).
     */
    public function nextIdentity(): ?SolicitudId;
}
