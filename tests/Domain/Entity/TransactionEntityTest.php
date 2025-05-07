<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\EventEntity;
use App\Domain\Entity\TransactionEntity;
use App\Domain\ValueObject\AddressValueObject;
use App\Domain\ValueObject\HashValueObject;
use App\Domain\ValueObject\WeiValueObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Domain\Entity\TransactionEntity
 */
class TransactionEntityTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testAddAndRemoveEvent(): void
    {
        $tx = $this->createTransaction('0x'.str_repeat('a', 64));
        $event1 = $this->createEvent('0x'.str_repeat('b', 64));
        $event2 = $this->createEvent('0x'.str_repeat('c', 64));

        $tx->addEvent($event1);
        $tx->addEvent($event2);
        $tx->removeEvent($event1->logIndex());

        $events = array_values($tx->events());
        $this->assertCount(1, $events);
        $this->assertSame($event2, $events[0]);
    }

    /**
     * @return void
     */
    public function testEventUniqueness(): void
    {
        $tx = $this->createTransaction('0x'.str_repeat('d', 64));
        $event = $this->createEvent('0x'.str_repeat('e', 64));

        $tx->addEvent($event);
        $tx->addEvent($event); // duplicate

        $this->assertCount(1, $tx->events());
    }

    /**
     * Creates a new TransactionEntity instance for testing purposes.
     *
     * @param string $hash
     *
     * @return TransactionEntity
     */
    private function createTransaction(string $hash): TransactionEntity
    {
        $address = new AddressValueObject('0x'.str_repeat('1', 40));
        $wei = new WeiValueObject('1000000000000000000');

        return new TransactionEntity(new HashValueObject($hash), $address, $address, $wei);
    }

    /**
     * Creates a new EventEntity instance for testing purposes.
     *
     * @param string $logIndex
     *
     * @return EventEntity
     */
    private function createEvent(string $logIndex): EventEntity
    {
        $address = new AddressValueObject('0x'.str_repeat('2', 40));

        return new EventEntity(new HashValueObject($logIndex), $address, 'EventName', []);
    }
}
