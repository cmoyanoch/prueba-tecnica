<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Events;

use SolicitudesModule\Domain\Enums\EstadoSolicitud;

/**
 * Evento emitido cuando cambia el estado de una solicitud.
 */
final readonly class SolicitudEstadoChangedEvent extends DomainEvent
{
    public function __construct(
        public int $solicitudId,
        public string $estadoAnterior,
        public string $estadoNuevo
    ) {
        parent::__construct();
    }

    public static function create(
        int $solicitudId,
        EstadoSolicitud $estadoAnterior,
        EstadoSolicitud $estadoNuevo
    ): self {
        return new self(
            solicitudId: $solicitudId,
            estadoAnterior: $estadoAnterior->value,
            estadoNuevo: $estadoNuevo->value
        );
    }

    public function eventName(): string
    {
        return 'solicitud.estado_changed';
    }

    public function toArray(): array
    {
        return [
            'solicitud_id' => $this->solicitudId,
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->estadoNuevo,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }
}
