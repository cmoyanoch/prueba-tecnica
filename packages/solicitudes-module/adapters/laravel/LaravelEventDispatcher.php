<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Laravel;

use Illuminate\Contracts\Events\Dispatcher;
use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Events\DomainEvent;

/**
 * Adaptador del despachador de eventos para Laravel.
 */
final class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function dispatch(DomainEvent $event): void
    {
        // Despacha el evento usando el nombre del evento como clave
        $this->dispatcher->dispatch($event->eventName(), [$event]);

        // También despacha el evento por su clase
        $this->dispatcher->dispatch($event);
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
