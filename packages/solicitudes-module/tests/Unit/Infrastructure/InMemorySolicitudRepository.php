<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Infrastructure;

use SolicitudesModule\Domain\Contracts\PaginatedResult;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Repositorio en memoria para testing.
 */
final class InMemorySolicitudRepository implements SolicitudRepositoryInterface
{
    /** @var array<int, Solicitud> */
    private array $solicitudes = [];
    private int $nextId = 1;

    public function save(Solicitud $solicitud): void
    {
        if ($solicitud->id() === null) {
            $solicitud->assignId(SolicitudId::fromInt($this->nextId++));
        }

        $this->solicitudes[$solicitud->id()->value()] = $solicitud;
    }

    public function findById(SolicitudId $id): ?Solicitud
    {
        return $this->solicitudes[$id->value()] ?? null;
    }

    public function findByIdOrFail(SolicitudId $id): Solicitud
    {
        $solicitud = $this->findById($id);

        if ($solicitud === null) {
            throw SolicitudNotFoundException::withId($id);
        }

        return $solicitud;
    }

    public function findAll(): array
    {
        return array_values($this->solicitudes);
    }

    public function findAllPaginated(int $page = 1, int $perPage = 15): PaginatedResult
    {
        $all = array_values($this->solicitudes);
        $total = count($all);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($all, $offset, $perPage);

        return PaginatedResult::create($items, $total, $perPage, $page);
    }

    public function findByEstado(EstadoSolicitud $estado): array
    {
        return array_values(array_filter(
            $this->solicitudes,
            fn(Solicitud $s) => $s->estado() === $estado
        ));
    }

    public function delete(Solicitud $solicitud): void
    {
        if ($solicitud->id() !== null) {
            unset($this->solicitudes[$solicitud->id()->value()]);
        }
    }

    public function deleteById(SolicitudId $id): bool
    {
        if (isset($this->solicitudes[$id->value()])) {
            unset($this->solicitudes[$id->value()]);
            return true;
        }

        return false;
    }

    public function count(): int
    {
        return count($this->solicitudes);
    }

    public function countByEstado(EstadoSolicitud $estado): int
    {
        return count($this->findByEstado($estado));
    }

    public function exists(SolicitudId $id): bool
    {
        return isset($this->solicitudes[$id->value()]);
    }

    public function nextIdentity(): ?SolicitudId
    {
        return SolicitudId::fromInt($this->nextId);
    }

    /**
     * Limpia todos los datos (útil para tests).
     */
    public function clear(): void
    {
        $this->solicitudes = [];
        $this->nextId = 1;
    }
}
