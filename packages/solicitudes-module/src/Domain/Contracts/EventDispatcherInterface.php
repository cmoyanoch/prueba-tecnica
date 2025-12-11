<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Contracts;

use SolicitudesModule\Domain\Events\DomainEvent;

/**
 * Contrato para el despachador de eventos.
 * Permite emitir eventos de dominio de forma desacoplada.
 */
interface EventDispatcherInterface
{
    /**
     * Despacha un evento de dominio.
     */
    public function dispatch(DomainEvent $event): void;

    /**
     * Despacha múltiples eventos.
     *
     * @param array<DomainEvent> $events
     */
    public function dispatchAll(array $events): void;
}
