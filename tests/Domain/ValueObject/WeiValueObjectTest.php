<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\WeiValueObject;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Domain\ValueObject\WeiValueObject
 */
class WeiValueObjectTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testValidWeiIsAccepted(): void
    {
        $wei = new WeiValueObject('1000000000000000000');

        $actual = $wei->value();

        $this->assertSame('1000000000000000000', $actual);
    }

    /**
     * @return void
     */
    public function testInvalidWeiThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new WeiValueObject('not-a-number');
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {
        $a = new WeiValueObject('100');
        $b = new WeiValueObject('100');
        $c = new WeiValueObject('200');

        $result1 = $a->equals($b);
        $result2 = $a->equals($c);

        $this->assertTrue($result1);
        $this->assertFalse($result2);
    }
}
