<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Services;

use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Events\DomainEvent;

/**
 * Implementación nula del despachador de eventos.
 * Útil para testing o cuando no se necesita eventos.
 */
final class NullEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(DomainEvent $event): void
    {
        // No-op
    }

    public function dispatchAll(array $events): void
    {
        // No-op
    }
}
