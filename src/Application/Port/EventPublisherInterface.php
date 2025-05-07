<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Event\DomainEventInterface;

/**
 * Port: Interface for publishing events to some place.
 */
interface EventPublisherInterface
{
    public function publish(DomainEventInterface $event): void;
}
