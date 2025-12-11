<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Events;

use DateTimeImmutable;

/**
 * Clase base para eventos de dominio.
 * Permite la comunicación desacoplada entre componentes.
 */
abstract readonly class DomainEvent
{
    public DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable();
    }

    /**
     * Nombre único del evento.
     */
    abstract public function eventName(): string;

    /**
     * Datos del evento para serialización.
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
