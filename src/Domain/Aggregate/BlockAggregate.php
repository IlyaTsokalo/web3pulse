<?php

declare(strict_types=1);

namespace App\Domain\Aggregate;

use App\Domain\Entity\BlockEntity;
use App\Domain\Entity\EntityInterface;
use App\Domain\ValueObject\HashValueObject;

/**
 * Aggregate root for Ethereum Block.
 *
 * Encapsulates the block and all its transactions. Only the aggregate root should be referenced outside.
 */
final class BlockAggregate implements AggregateRootInterface
{
    /**
     * @param BlockEntity $block
     */
    public function __construct(
        private BlockEntity $block
    ) {
    }

    /**
     * @return HashValueObject
     */
    public function getId(): HashValueObject
    {
        return $this->block->hash();
    }

    /**
     * @return int
     */
    public function number(): int
    {
        return $this->block->number();
    }

    /**
     * @param EntityInterface $other
     *
     * @return bool
     */
    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->getId()->equals($other->getId());
    }

    /**
     * Returns the root BlockEntity for persistence.
     *
     * @return BlockEntity
     */
    public function getPersistenceRoot(): BlockEntity
    {
        return $this->block;
    }

    /**
     * Reconstitutes an aggregate from a BlockEntity.
     *
     * @param BlockEntity $blockEntity
     *
     * @return self
     */
    public static function reconstitute(BlockEntity $blockEntity): self
    {
        return new self($blockEntity);
    }
}
