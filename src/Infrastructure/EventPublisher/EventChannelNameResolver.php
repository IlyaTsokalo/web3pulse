<?php

declare(strict_types=1);

namespace App\Infrastructure\EventPublisher;

use App\Domain\Event\DomainEventInterface;

/**
 * Determines Redis channel names based on domain events.
 */
class EventChannelNameResolver
{
    /**
     * @param DomainEventInterface $event
     *
     * @return string
     */
    public function resolveChannelName(DomainEventInterface $event): string
    {
        return sprintf('event.%s', $event->getEventName());
    }
}
