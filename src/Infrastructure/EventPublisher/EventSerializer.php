<?php

declare(strict_types=1);

namespace App\Infrastructure\EventPublisher;

use App\Domain\Entity\EntityInterface;
use App\Domain\Event\DomainEventInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Serializes domain events to JSON for Redis.
 */
class EventSerializer
{
    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(protected SerializerInterface $serializer)
    {
    }

    /**
     * @param DomainEventInterface $event
     *
     * @return string
     */
    public function serialize(DomainEventInterface $event): string
    {
        return $this
            ->serializer
            ->serialize(
                $event,
                'json',
                [
                    'circular_reference_handler' => function (EntityInterface $object) {
                        return $object->getId();
                    },
                ]
            );
    }
}
