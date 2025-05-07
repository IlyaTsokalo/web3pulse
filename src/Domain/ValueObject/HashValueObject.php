<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Represents an Ethereum hash (0x-prefixed, 64 hex chars).
 */
final class HashValueObject
{
    /**
     * @var string Ethereum hash (lowercase)
     */
    protected string $value;

    /**
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $value)) {
            throw new InvalidArgumentException('Invalid Ethereum hash: '.$value);
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
     * @param HashValueObject $other
     *
     * @return bool
     */
    public function equals(HashValueObject $other): bool
    {
        return $this->value === $other->value;
    }
}
