<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Aggregate\BlockAggregate;
use App\Domain\ValueObject\HashValueObject;
use DateMalformedStringException;
use DateTimeImmutable;

/**
 * @property int               $number
 * @property string            $hash
 * @property DateTimeImmutable $timestamp
 * @property DateTimeImmutable $createdAt
 */
class BlockDTO
{
    /**
     * @param int                    $number
     * @param string                 $hash
     * @param DateTimeImmutable      $timestamp
     * @param DateTimeImmutable|null $createdAt
     */
    public function __construct(
        public int $number,
        public string $hash,
        public DateTimeImmutable $timestamp,
        public ?DateTimeImmutable $createdAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    /**
     * Create BlockDTO from BlockAggregate.
     *
     * @return static
     */
    public static function fromDomain(BlockAggregate $aggregate): self
    {
        $block = $aggregate->getPersistenceRoot();

        return new self(
            $block->number(),
            $block->hash() instanceof HashValueObject ? $block->hash()->value() : $block->hash(),
            $block->timestamp(),
            $block->createdAt()
        );
    }

    /**
     * @param array $data
     *
     * @throws DateMalformedStringException
     *
     * @return self
     */
    public static function fromWebSocketData(array $data): self
    {
        return new self(
            hexdec($data['number']),
            $data['hash'],
            new DateTimeImmutable('@'.hexdec($data['timestamp'])),
        );
    }
}
