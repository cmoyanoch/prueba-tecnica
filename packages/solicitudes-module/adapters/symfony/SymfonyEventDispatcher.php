<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Symfony;

use SolicitudesModule\Domain\Contracts\EventDispatcherInterface;
use SolicitudesModule\Domain\Events\DomainEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyDispatcher;

/**
 * Adaptador del despachador de eventos para Symfony.
 */
final class SymfonyEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly SymfonyDispatcher $dispatcher
    ) {}

    public function dispatch(DomainEvent $event): void
    {
        // Envuelve el evento de dominio en un evento de Symfony
        $symfonyEvent = new SymfonyDomainEventWrapper($event);
        
        // Despacha usando el nombre del evento
        $this->dispatcher->dispatch($symfonyEvent, $event->eventName());
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
