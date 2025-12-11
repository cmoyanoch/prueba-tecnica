<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SolicitudesModule\Application\DTOs\CreateSolicitudDTO;
use SolicitudesModule\Application\DTOs\ListSolicitudesDTO;
use SolicitudesModule\Application\DTOs\UpdateEstadoDTO;
use SolicitudesModule\Application\UseCases\CreateSolicitudUseCase;
use SolicitudesModule\Application\UseCases\DeleteSolicitudUseCase;
use SolicitudesModule\Application\UseCases\GetSolicitudUseCase;
use SolicitudesModule\Application\UseCases\ListSolicitudesUseCase;
use SolicitudesModule\Application\UseCases\UpdateEstadoSolicitudUseCase;
use SolicitudesModule\Domain\Exceptions\DomainException;

/**
 * Controlador API para Solicitudes en Laravel.
 */
class SolicitudController extends Controller
{
    public function __construct(
        private readonly ListSolicitudesUseCase $listUseCase,
        private readonly GetSolicitudUseCase $getUseCase,
        private readonly CreateSolicitudUseCase $createUseCase,
        private readonly UpdateEstadoSolicitudUseCase $updateEstadoUseCase,
        private readonly DeleteSolicitudUseCase $deleteUseCase
    ) {}

    /**
     * Listar solicitudes paginadas.
     */
    public function index(Request $request): JsonResponse
    {
        $dto = ListSolicitudesDTO::fromArray($request->all());
        $result = $this->listUseCase->execute($dto);

        return response()->json($result->toArray());
    }

    /**
     * Obtener una solicitud por ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $solicitud = $this->getUseCase->execute($id);
            return response()->json(['data' => $solicitud->toArray()]);
        } catch (DomainException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], 404);
        }
    }

    /**
     * Crear una nueva solicitud.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_documento' => 'required|string|min:3|max:255',
        ]);

        try {
            $dto = CreateSolicitudDTO::fromArray($request->all());
            $solicitud = $this->createUseCase->execute($dto);

            return response()->json([
                'data' => $solicitud->toArray(),
                'message' => 'Solicitud creada correctamente',
            ], 201);
        } catch (DomainException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], 422);
        }
    }

    /**
     * Actualizar el estado de una solicitud.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'estado' => 'required|string|in:pendiente,aprobado,rechazado,modificar',
        ]);

        try {
            $dto = new UpdateEstadoDTO(
                solicitudId: $id,
                estado: \SolicitudesModule\Domain\Enums\EstadoSolicitud::from($request->input('estado'))
            );
            
            $solicitud = $this->updateEstadoUseCase->execute($dto);

            return response()->json([
                'data' => $solicitud->toArray(),
                'message' => 'Estado actualizado correctamente',
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], 422);
        }
    }

    /**
     * Eliminar una solicitud.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->deleteUseCase->execute($id);

            if ($deleted) {
                return response()->json([
                    'message' => 'Solicitud eliminada correctamente',
                ]);
            }

            return response()->json([
                'error' => 'No se pudo eliminar la solicitud',
            ], 422);
        } catch (DomainException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], 404);
        }
    }
}
