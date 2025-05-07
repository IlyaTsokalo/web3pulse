<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\AddressValueObject;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Domain\ValueObject\AddressValueObject
 */
class AddressValueObjectTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testValidAddressIsAccepted(): void
    {
        $address = new AddressValueObject('0x1234567890abcdef1234567890abcdef12345678');

        $actual = $address->value();

        $this->assertSame('0x1234567890abcdef1234567890abcdef12345678', $actual);
    }

    /**
     * @return void
     */
    public function testAddressIsLowercased(): void
    {
        $address = new AddressValueObject('0xABCDEFabcdefABCDEFabcdefABCDEFabcdefABCD');

        $actual = $address->value();

        $this->assertSame('0xabcdefabcdefabcdefabcdefabcdefabcdefabcd', $actual);
    }

    /**
     * @return void
     */
    public function testInvalidAddressThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AddressValueObject('invalid');
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {
        $a = new AddressValueObject('0x1234567890abcdef1234567890abcdef12345678');
        $b = new AddressValueObject('0x1234567890abcdef1234567890abcdef12345678');
        $c = new AddressValueObject('0xabcdefabcdefabcdefabcdefabcdefabcdefabcd');

        $result1 = $a->equals($b);
        $result2 = $a->equals($c);

        $this->assertTrue($result1);
        $this->assertFalse($result2);
    }
}
