<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Events;

use SolicitudesModule\Domain\Entities\Solicitud;

/**
 * Evento emitido cuando se crea una solicitud.
 */
final readonly class SolicitudCreatedEvent extends DomainEvent
{
    public function __construct(
        public int $solicitudId,
        public string $nombreDocumento,
        public string $estado
    ) {
        parent::__construct();
    }

    public static function fromEntity(Solicitud $solicitud): self
    {
        return new self(
            solicitudId: $solicitud->id()?->value() ?? 0,
            nombreDocumento: $solicitud->nombreDocumento()->value(),
            estado: $solicitud->estado()->value
        );
    }

    public function eventName(): string
    {
        return 'solicitud.created';
    }

    public function toArray(): array
    {
        return [
            'solicitud_id' => $this->solicitudId,
            'nombre_documento' => $this->nombreDocumento,
            'estado' => $this->estado,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }
}
