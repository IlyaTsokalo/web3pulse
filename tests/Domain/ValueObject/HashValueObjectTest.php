<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\HashValueObject;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Domain\ValueObject\HashValueObject
 */
class HashValueObjectTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testValidHashIsAccepted(): void
    {
        $hash = new HashValueObject('0x'.str_repeat('a', 64));

        $actual = $hash->value();

        $this->assertSame('0x'.str_repeat('a', 64), $actual);
    }

    /**
     * @return void
     */
    public function testHashIsLowercased(): void
    {
        $hash = new HashValueObject('0x'.strtoupper(str_repeat('b', 64)));

        $actual = $hash->value();

        $this->assertSame('0x'.str_repeat('b', 64), $actual);
    }

    /**
     * @return void
     */
    public function testInvalidHashThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new HashValueObject('invalid');
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {
        $a = new HashValueObject('0x'.str_repeat('1', 64));
        $b = new HashValueObject('0x'.str_repeat('1', 64));
        $c = new HashValueObject('0x'.str_repeat('2', 64));

        $result1 = $a->equals($b);
        $result2 = $a->equals($c);

        $this->assertTrue($result1);
        $this->assertFalse($result2);
    }
}
