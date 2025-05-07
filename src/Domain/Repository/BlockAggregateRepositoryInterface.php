<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Aggregate\BlockAggregate;
use App\Domain\ValueObject\HashValueObject;

/**
 * Port: Repository interface for BlockAggregate persistence.
 *
 * Defines the contract for saving and retrieving BlockAggregates.
 */
interface BlockAggregateRepositoryInterface
{
    /**
     * @param BlockAggregate $block
     *
     * @return void
     */
    public function save(BlockAggregate $block): void;

    /**
     * @param HashValueObject $hash
     *
     * @return BlockAggregate|null
     */
    public function findByHash(HashValueObject $hash): ?BlockAggregate;
}
