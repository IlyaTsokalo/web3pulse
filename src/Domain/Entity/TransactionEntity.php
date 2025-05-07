<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\AddressValueObject;
use App\Domain\ValueObject\HashValueObject;
use App\Domain\ValueObject\WeiValueObject;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an Ethereum transaction.
 */
#[ORM\Entity]
#[ORM\Table(name: 'transactions')]
final class TransactionEntity implements EntityInterface
{
    /**
     * @param HashValueObject    $hash   Transaction hash (identity)
     * @param AddressValueObject $from   Sender address
     * @param AddressValueObject $to     Recipient address
     * @param WeiValueObject     $value  Value transferred
     * @param EventEntity[]      $events Events emitted by the transaction
     */
    #[ORM\Id]
    #[ORM\Column(type: 'string', unique: true)]
    private string $hashValue;

    #[ORM\Column(type: 'string')]
    private string $fromAddress;

    #[ORM\Column(type: 'string')]
    private string $toAddress;

    #[ORM\Column(type: 'string')]
    private string $value;

    /**
     * @var EventEntity[]
     */
    private array $events = [];

    // Events mapping will require a relation, left as TODO for now

    public function __construct(
        HashValueObject $hash,
        AddressValueObject $from,
        AddressValueObject $to,
        WeiValueObject $value,
        /* @var EventEntity[] */
        array $events = []
    ) {
        $this->hashValue = $hash->value();
        $this->fromAddress = $from->value();
        $this->toAddress = $to->value();
        $this->value = $value->value();
        $this->events = $events;
    }

    /**
     * @return HashValueObject
     */
    public function hash(): HashValueObject
    {
        return new HashValueObject($this->hashValue);
    }

    /**
     * @return AddressValueObject
     */
    public function from(): AddressValueObject
    {
        return new AddressValueObject($this->fromAddress);
    }

    /**
     * @return AddressValueObject
     */
    public function to(): AddressValueObject
    {
        return new AddressValueObject($this->toAddress);
    }

    /**
     * @return WeiValueObject
     */
    public function value(): WeiValueObject
    {
        return new WeiValueObject($this->value);
    }

    /**
     * @return EventEntity[]
     */
    public function events(): array
    {
        return $this->events;
    }

    /**
     * Add an event to the transaction.
     *
     * @param EventEntity $event
     *
     * @return void
     */
    public function addEvent(EventEntity $event): void
    {
        foreach ($this->events as $existing) {
            if ($existing->equals($event)) {
                return;
            }
        }
        $this->events[] = $event;
    }

    /**
     * Remove an event by log index.
     *
     * @param HashValueObject $logIndex
     *
     * @return void
     */
    public function removeEvent(HashValueObject $logIndex): void
    {
        $this->events = array_filter(
            $this->events,
            fn (EventEntity $e) => !$e->logIndex()->equals($logIndex)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): HashValueObject
    {
        return $this->hash();
    }

    /**
     * {@inheritDoc}
     */
    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->getId()->equals($other->getId());
    }
}
