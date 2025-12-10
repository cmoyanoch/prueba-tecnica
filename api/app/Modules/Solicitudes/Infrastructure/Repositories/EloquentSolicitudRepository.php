<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Infrastructure\Repositories;

use App\Modules\Solicitudes\Domain\Entities\Solicitud;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EloquentSolicitudRepository implements SolicitudRepositoryInterface
{
    public function __construct(
        private readonly Solicitud $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model
            ->newQuery()
            ->orderByDesc('id')
            ->get();
    }

    public function getAllPaginated(int $perPage = 5): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findById(int $id): Solicitud
    {
        return $this->model
            ->newQuery()
            ->findOrFail($id);
    }

    public function create(array $data): Solicitud
    {
        return $this->model
            ->newQuery()
            ->create($data);
    }

    public function updateEstado(int $id, EstadoSolicitud $estado): Solicitud
    {
        $solicitud = $this->findById($id);
        $solicitud->update(['estado' => $estado->value]);
        return $solicitud->fresh();
    }

    public function delete(int $id): bool
    {
        $solicitud = $this->findById($id);
        return $solicitud->delete();
    }
}
