<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Symfony;

use SolicitudesModule\Domain\Events\DomainEvent;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Wrapper para eventos de dominio en Symfony.
 */
final class SymfonyDomainEventWrapper extends Event
{
    public function __construct(
        public readonly DomainEvent $domainEvent
    ) {}
}
