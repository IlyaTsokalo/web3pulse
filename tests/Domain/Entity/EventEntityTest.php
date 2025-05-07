<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\EventEntity;
use App\Domain\ValueObject\AddressValueObject;
use App\Domain\ValueObject\HashValueObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Domain\Entity\EventEntity
 */
class EventEntityTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testIdentityAndEquality(): void
    {
        $address = $this->createAddress('0x'.str_repeat('3', 40));
        $logIndex1 = new HashValueObject('0x'.str_repeat('a', 64));
        $logIndex2 = new HashValueObject('0x'.str_repeat('b', 64));

        $event1 = new EventEntity($logIndex1, $address, 'Event', []);
        $event2 = new EventEntity($logIndex1, $address, 'Event', []);
        $event3 = new EventEntity($logIndex2, $address, 'Event', []);

        $this->assertTrue($event1->equals($event2));
        $this->assertFalse($event1->equals($event3));
        $this->assertSame($logIndex1, $event1->getId());
    }

    /**
     * Creates a new AddressValueObject for testing purposes.
     *
     * @param string $address
     *
     * @return AddressValueObject
     */
    private function createAddress(string $address): AddressValueObject
    {
        return new AddressValueObject($address);
    }
}
