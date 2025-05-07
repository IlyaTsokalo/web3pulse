<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Repository\BlockAggregateRepositoryInterface;

/**
 * Domain service for validating blocks.
 */
class BlockValidationService
{
    /**
     * @param BlockAggregateRepositoryInterface $repository
     */
    public function __construct(
        private BlockAggregateRepositoryInterface $repository
    ) {
    }

    /**
     * @param BlockAggregate $block
     *
     * @throws DomainException If block hash is not unique
     *
     * @return void
     */
    public function ensureBlockIsValid(BlockAggregate $block): void
    {
        if ($this->repository->findByHash($block->getId())) {
            throw new \DomainException('Block is not valid!');
        }
    }
}
