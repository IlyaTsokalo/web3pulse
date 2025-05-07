<?php

declare(strict_types=1);

namespace App\Domain\Event;

use DateTimeImmutable;

/**
 * Domain event representing a new Ethereum block being registered in the system.
 */
class BlockRegisteredEvent implements DomainEventInterface
{
    /**
     * @param string            $blockHash
     * @param int               $blockNumber
     * @param DateTimeImmutable $timestamp
     */
    public function __construct(
        protected readonly string $blockHash,
        protected readonly int $blockNumber,
        protected readonly DateTimeImmutable $timestamp
    ) {
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'block.registered';
    }
}
