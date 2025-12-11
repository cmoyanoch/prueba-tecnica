<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Services;

use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Events\DomainEvent;

/**
 * Despachador de eventos en memoria.
 * Útil para testing y desarrollo.
 */
final class InMemoryEventDispatcher implements EventDispatcherInterface
{
    /** @var array<DomainEvent> */
    private array $dispatchedEvents = [];

    /** @var array<string, array<callable>> */
    private array $listeners = [];

    public function dispatch(DomainEvent $event): void
    {
        $this->dispatchedEvents[] = $event;

        $eventName = $event->eventName();
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $listener($event);
            }
        }
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    /**
     * Registra un listener para un evento.
     */
    public function listen(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * Obtiene todos los eventos despachados.
     *
     * @return array<DomainEvent>
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }

    /**
     * Limpia los eventos despachados.
     */
    public function clear(): void
    {
        $this->dispatchedEvents = [];
    }
}
