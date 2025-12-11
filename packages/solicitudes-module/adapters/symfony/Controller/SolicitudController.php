<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Symfony\Controller;

use SolicitudesModule\Application\DTOs\CreateSolicitudDTO;
use SolicitudesModule\Application\DTOs\ListSolicitudesDTO;
use SolicitudesModule\Application\DTOs\UpdateEstadoDTO;
use SolicitudesModule\Application\UseCases\CreateSolicitudUseCase;
use SolicitudesModule\Application\UseCases\DeleteSolicitudUseCase;
use SolicitudesModule\Application\UseCases\GetSolicitudUseCase;
use SolicitudesModule\Application\UseCases\ListSolicitudesUseCase;
use SolicitudesModule\Application\UseCases\UpdateEstadoSolicitudUseCase;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador API para Solicitudes en Symfony.
 */
#[Route('/api/solicitudes', name: 'solicitudes_')]
class SolicitudController extends AbstractController
{
    public function __construct(
        private readonly ListSolicitudesUseCase $listUseCase,
        private readonly GetSolicitudUseCase $getUseCase,
        private readonly CreateSolicitudUseCase $createUseCase,
        private readonly UpdateEstadoSolicitudUseCase $updateEstadoUseCase,
        private readonly DeleteSolicitudUseCase $deleteUseCase
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $dto = ListSolicitudesDTO::fromArray($request->query->all());
        $result = $this->listUseCase->execute($dto);

        return $this->json($result->toArray());
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $solicitud = $this->getUseCase->execute($id);
            return $this->json(['data' => $solicitud->toArray()]);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('', name: 'store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['nombre_documento'])) {
            return $this->json([
                'error' => 'El campo nombre_documento es requerido',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = CreateSolicitudDTO::fromArray($data);
            $solicitud = $this->createUseCase->execute($dto);

            return $this->json([
                'data' => $solicitud->toArray(),
                'message' => 'Solicitud creada correctamente',
            ], Response::HTTP_CREATED);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['estado'])) {
            return $this->json([
                'error' => 'El campo estado es requerido',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = new UpdateEstadoDTO(
                solicitudId: $id,
                estado: EstadoSolicitud::from($data['estado'])
            );
            
            $solicitud = $this->updateEstadoUseCase->execute($dto);

            return $this->json([
                'data' => $solicitud->toArray(),
                'message' => 'Estado actualizado correctamente',
            ]);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\ValueError) {
            return $this->json([
                'error' => 'Estado inválido',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/{id}', name: 'destroy', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->deleteUseCase->execute($id);

            if ($deleted) {
                return $this->json([
                    'message' => 'Solicitud eliminada correctamente',
                ]);
            }

            return $this->json([
                'error' => 'No se pudo eliminar la solicitud',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
