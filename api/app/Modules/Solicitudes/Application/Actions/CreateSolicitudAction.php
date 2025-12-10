<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Application\Actions;

use App\Modules\Solicitudes\Application\DTOs\CreateSolicitudDTO;
use App\Modules\Solicitudes\Application\Services\AuditLoggerInterface;
use App\Modules\Solicitudes\Domain\Contracts\SolicitudRepositoryInterface;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;

final readonly class CreateSolicitudAction
{
    public function __construct(
        private SolicitudRepositoryInterface $repository,
        private AuditLoggerInterface $auditLogger
    ) {}

    public function execute(CreateSolicitudDTO $dto): object
    {
        $solicitud = $this->repository->create([
            'nombre_documento' => $dto->nombreDocumento,
            'estado' => EstadoSolicitud::PENDIENTE->value,
        ]);

        // Log de auditoría
        $this->auditLogger->logSolicitudCreated(
            $solicitud->id,
            $solicitud->nombre_documento
        );

        return $solicitud;
    }
}
