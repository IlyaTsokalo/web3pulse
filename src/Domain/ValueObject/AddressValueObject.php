<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Represents an Ethereum address (0x-prefixed, 40 hex chars).
 */
final class AddressValueObject
{
    /**
     * @var string Ethereum address (lowercase)
     */
    protected string $value;

    /**
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        // Ethereum address validation (0x-prefixed, 40 hex chars)
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
            throw new InvalidArgumentException('Invalid Ethereum address: '.$value);
        }
        $this->value = strtolower($value);
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param AddressValueObject $other
     *
     * @return bool
     */
    public function equals(AddressValueObject $other): bool
    {
        return $this->value === $other->value;
    }
}
