<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\EventEntity;

/**
 * @property string $logIndex
 * @property string $address
 * @property string $eventName
 * @property array  $parameters
 */
class EventDTO
{
    /**
     * @param string $logIndex
     * @param string $address
     * @param string $eventName
     * @param array  $parameters
     */
    public function __construct(
        public string $logIndex,
        public string $address,
        public string $eventName,
        public array $parameters = []
    ) {
    }

    /**
     * Create EventDTO from EventEntity.
     *
     * @param EventEntity $event
     *
     * @return static
     */
    public static function fromDomain(EventEntity $event): self
    {
        return new self(
            $event->logIndex()->value(),
            $event->address()->value(),
            $event->eventName(),
            $event->parameters()
        );
    }
}
