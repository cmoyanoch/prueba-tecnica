<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Persistence\Eloquent;

use DateTimeImmutable;
use SolicitudesModule\Domain\Contracts\PaginatedResult;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Implementación del repositorio usando Eloquent (Laravel).
 * Este archivo usa el Model de Eloquent internamente pero expone la entidad de dominio.
 */
final class EloquentSolicitudRepository implements SolicitudRepositoryInterface
{
    public function __construct(
        private readonly EloquentSolicitudModel $model
    ) {}

    public function save(Solicitud $solicitud): void
    {
        $data = [
            'nombre_documento' => $solicitud->nombreDocumento()->value(),
            'estado' => $solicitud->estado()->value,
            'created_at' => $solicitud->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $solicitud->updatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($solicitud->id() !== null) {
            // Update
            $this->model->newQuery()
                ->where('id', $solicitud->id()->value())
                ->update($data);
        } else {
            // Insert
            $eloquentModel = $this->model->newQuery()->create($data);
            $solicitud->assignId(SolicitudId::fromInt($eloquentModel->id));
        }
    }

    public function findById(SolicitudId $id): ?Solicitud
    {
        $model = $this->model->newQuery()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->toDomainEntity($model);
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
        $models = $this->model->newQuery()
            ->orderByDesc('id')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->all();
    }

    public function findAllPaginated(int $page = 1, int $perPage = 15): PaginatedResult
    {
        $paginator = $this->model->newQuery()
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = collect($paginator->items())
            ->map(fn($model) => $this->toDomainEntity($model))
            ->all();

        return PaginatedResult::create(
            items: $items,
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage()
        );
    }

    public function findByEstado(EstadoSolicitud $estado): array
    {
        $models = $this->model->newQuery()
            ->where('estado', $estado->value)
            ->orderByDesc('id')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->all();
    }

    public function delete(Solicitud $solicitud): void
    {
        if ($solicitud->id() === null) {
            return;
        }

        $this->model->newQuery()
            ->where('id', $solicitud->id()->value())
            ->delete();
    }

    public function deleteById(SolicitudId $id): bool
    {
        return $this->model->newQuery()
            ->where('id', $id->value())
            ->delete() > 0;
    }

    public function count(): int
    {
        return $this->model->newQuery()->count();
    }

    public function countByEstado(EstadoSolicitud $estado): int
    {
        return $this->model->newQuery()
            ->where('estado', $estado->value)
            ->count();
    }

    public function exists(SolicitudId $id): bool
    {
        return $this->model->newQuery()
            ->where('id', $id->value())
            ->exists();
    }

    public function nextIdentity(): ?SolicitudId
    {
        // Eloquent usa auto-increment, no necesitamos generar IDs
        return null;
    }

    /**
     * Convierte un modelo Eloquent a entidad de dominio.
     */
    private function toDomainEntity(EloquentSolicitudModel $model): Solicitud
    {
        return Solicitud::reconstitute(
            id: SolicitudId::fromInt($model->id),
            nombreDocumento: NombreDocumento::fromString($model->nombre_documento),
            estado: EstadoSolicitud::from($model->estado),
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at)
        );
    }
}
