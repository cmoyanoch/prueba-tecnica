<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Domain\Contracts;

use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SolicitudRepositoryInterface
{
    public function getAll(): Collection;
    public function getAllPaginated(int $perPage = 5): LengthAwarePaginator;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function updateEstado(int $id, EstadoSolicitud $estado): object;
    public function delete(int $id): bool;
}
