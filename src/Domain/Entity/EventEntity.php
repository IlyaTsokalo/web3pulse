<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\AddressValueObject;
use App\Domain\ValueObject\HashValueObject;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an Ethereum event (log).
 */
#[ORM\Entity]
#[ORM\Table(name: 'events')]
final class EventEntity implements EntityInterface
{
    /**
     * @param HashValueObject    $logIndex   Log index in the block (identity for simplicity)
     * @param AddressValueObject $address    Contract address that emitted the event
     * @param string             $eventName  Name of the event
     * @param array              $parameters Event parameters (decoded)
     *
     * @return void
     */
    #[ORM\Id]
    #[ORM\Column(name: 'log_index_value', type: 'string', unique: true)]
    private string $logIndexValue;

    #[ORM\Column(name: 'address_value', type: 'string')]
    private string $addressValue;

    #[ORM\Column(type: 'string')]
    private string $eventName;

    #[ORM\Column(type: 'json')]
    private array $parameters;

    public function __construct(
        protected HashValueObject $logIndex,
        protected AddressValueObject $address,
        string $eventName,
        array $parameters = []
    ) {
        $this->logIndexValue = $logIndex->value();
        $this->addressValue = $address->value();
        $this->eventName = $eventName;
        $this->parameters = $parameters;
    }

    /**
     * @return HashValueObject
     */
    public function logIndex(): HashValueObject
    {
        return $this->logIndex;
    }

    /**
     * @return AddressValueObject
     */
    public function address(): AddressValueObject
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function eventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     *
     * @return HashValueObject
     */
    public function getId(): HashValueObject
    {
        return $this->logIndex();
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->getId()->equals($other->getId());
    }

    /**
     * Get the log index value string.
     */
    public function logIndexValue(): string
    {
        return $this->logIndexValue;
    }

    /**
     * Get the address value string.
     */
    public function addressValue(): string
    {
        return $this->addressValue;
    }
}
