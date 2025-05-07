<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Represents a Wei amount (smallest denomination of Ether).
 */
final class WeiValueObject
{
    /**
     * @var string Wei value (as string for precision)
     */
    protected string $value;

    /**
     * @param string $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        if (!preg_match('/^\d+$/', $value)) {
            throw new InvalidArgumentException('Invalid Wei value: '.$value);
        }
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param WeiValueObject $other
     *
     * @return bool
     */
    public function equals(WeiValueObject $other): bool
    {
        return $this->value === $other->value;
    }
}
