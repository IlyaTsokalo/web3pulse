<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Port\EventPublisherInterface;
use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Event\BlockRegisteredEvent;
use App\Domain\Repository\BlockAggregateRepositoryInterface;
use App\Domain\Service\BlockValidationService;

/**
 * Application service for registering a new block.
 */
class RegisterBlockService
{
    /**
     * @param BlockAggregateRepositoryInterface $repository
     * @param BlockValidationService            $blockValidationService
     * @param EventPublisherInterface           $eventPublisher
     */
    public function __construct(
        protected BlockAggregateRepositoryInterface $repository,
        protected BlockValidationService $blockValidationService,
        protected EventPublisherInterface $eventPublisher
    ) {
    }

    /**
     * Registers a new block aggregate after validation.
     *
     * @param BlockAggregate $block
     *
     * @return void
     */
    public function registerBlock(BlockAggregate $block): void
    {
        $this->blockValidationService->ensureBlockIsValid($block);

        $this->repository->save($block);

        $event = new BlockRegisteredEvent(
            $block->getPersistenceRoot()->hash()->value(),
            $block->number(),
            $block->getPersistenceRoot()->timestamp()
        );

        $this->eventPublisher->publish($event);
    }
}
