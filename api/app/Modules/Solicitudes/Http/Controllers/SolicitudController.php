<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Solicitudes\Application\Actions\CreateSolicitudAction;
use App\Modules\Solicitudes\Application\Actions\ListSolicitudesAction;
use App\Modules\Solicitudes\Application\Actions\UpdateEstadoSolicitudAction;
use App\Modules\Solicitudes\Application\Actions\DeleteSolicitudAction;
use App\Modules\Solicitudes\Application\DTOs\CreateSolicitudDTO;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use App\Modules\Solicitudes\Http\Requests\IndexSolicitudRequest;
use App\Modules\Solicitudes\Http\Requests\StoreSolicitudRequest;
use App\Modules\Solicitudes\Http\Requests\UpdateSolicitudRequest;
use App\Modules\Solicitudes\Http\Resources\SolicitudResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SolicitudController extends Controller
{
    public function index(
        IndexSolicitudRequest $request,
        ListSolicitudesAction $action
    ): AnonymousResourceCollection {
        $perPage = (int) $request->validated('per_page', 5);

        if ($perPage > 0) {
            $paginator = $action->executePaginated($perPage);
            return SolicitudResource::collection($paginator);
        }

        $solicitudes = $action->execute();
        return SolicitudResource::collection($solicitudes);
    }

    public function store(
        StoreSolicitudRequest $request,
        CreateSolicitudAction $action
    ): JsonResponse {
        $dto = CreateSolicitudDTO::fromArray($request->validated());
        $solicitud = $action->execute($dto);
        return (new SolicitudResource($solicitud))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        int $id,
        UpdateSolicitudRequest $request,
        UpdateEstadoSolicitudAction $action
    ): SolicitudResource {
        $estado = EstadoSolicitud::from($request->validated('estado'));
        $solicitud = $action->execute($id, $estado);
        return new SolicitudResource($solicitud);
    }

    public function destroy(
        int $id,
        DeleteSolicitudAction $action
    ): JsonResponse {
        $deleted = $action->execute($id);

        if ($deleted) {
            return response()->json(['message' => 'Solicitud eliminada correctamente'], 200);
        }

        return response()->json(['message' => 'Error al eliminar la solicitud'], 500);
    }
}
