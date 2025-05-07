<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\HashValueObject;
use App\Infrastructure\Repository\DatabaseBlockAggregateRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an Ethereum block.
 */
#[ORM\Entity(repositoryClass: DatabaseBlockAggregateRepository::class)]
#[ORM\Table(name: 'blocks')]
final class BlockEntity implements EntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    private int $number;

    #[ORM\Column(name: 'hash_value', type: 'string', length: 66)]
    private string $hashValue;

    private HashValueObject $hash;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $timestamp;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @param int               $number    Block number
     * @param HashValueObject   $hash      Block hash
     * @param DateTimeImmutable $timestamp Block timestamp
     * @param DateTimeImmutable $createdAt Timestamp when the block was created
     */
    public function __construct(
        int $number,
        HashValueObject $hash,
        DateTimeImmutable $timestamp,
        DateTimeImmutable $createdAt
    ) {
        $this->number = $number;
        $this->hashValue = $hash->value();
        $this->hash = $hash;
        $this->timestamp = $timestamp;
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function number(): int
    {
        return $this->number;
    }

    /**
     * @return HashValueObject
     */
    public function hash(): HashValueObject
    {
        return $this->hash;
    }

    /**
     * @return DateTimeImmutable
     */
    public function timestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @return DateTimeImmutable
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Get the hash value string.
     */
    public function hashValue(): string
    {
        return $this->hashValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): int
    {
        return $this->number;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->getId() === $other->getId();
    }
}
