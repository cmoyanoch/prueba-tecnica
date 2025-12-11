<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Events;

/**
 * Evento emitido cuando se elimina una solicitud.
 */
final readonly class SolicitudDeletedEvent extends DomainEvent
{
    public function __construct(
        public int $solicitudId,
        public string $nombreDocumento
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'solicitud.deleted';
    }

    public function toArray(): array
    {
        return [
            'solicitud_id' => $this->solicitudId,
            'nombre_documento' => $this->nombreDocumento,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }
}
