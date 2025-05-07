<?php

declare(strict_types=1);

namespace App\Domain\Event;

interface DomainEventInterface
{
    /**
     * @return string
     */
    public function getEventName(): string;
}
