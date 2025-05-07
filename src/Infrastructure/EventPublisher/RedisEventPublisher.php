<?php

declare(strict_types=1);

namespace App\Infrastructure\EventPublisher;

use App\Application\Port\EventPublisherInterface;
use App\Domain\Event\DomainEventInterface;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Adapter: Publishes events to Redis channels.
 */
class RedisEventPublisher implements EventPublisherInterface
{
    /**
     * @param Client                   $redisClient
     * @param EventSerializer          $eventSerializer
     * @param EventChannelNameResolver $channelNameResolver
     * @param LoggerInterface          $logger
     */
    public function __construct(
        protected Client $redisClient,
        protected EventSerializer $eventSerializer,
        protected EventChannelNameResolver $channelNameResolver,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param DomainEventInterface $event
     *
     * @return void
     */
    public function publish(DomainEventInterface $event): void
    {
        try {
            $redisChannel = $this->channelNameResolver->resolveChannelName($event);

            $serializedEvent = $this->eventSerializer->serialize($event);

            $this->redisClient->publish($redisChannel, $serializedEvent);

            $this->logger->info('Event published to Redis', [
                'event_type' => $event->getEventName(),
                'channel' => $redisChannel,
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Failed to publish event to Redis', [
                'event_type' => $event->getEventName(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
