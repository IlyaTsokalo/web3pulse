<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Entity\BlockEntity;
use App\Domain\Repository\BlockAggregateRepositoryInterface;
use App\Domain\Service\BlockValidationService;
use App\Domain\ValueObject\HashValueObject;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlockValidationServiceTest extends KernelTestCase
{
    public function testBlockIsValidWhenHashIsUnique(): void
    {
        $repository = $this->createMock(BlockAggregateRepositoryInterface::class);
        $repository->method('findByHash')->willReturn(null);
        $block = $this->getBlockAggregateWithId('0x'.str_repeat('a', 64));
        $service = new BlockValidationService($repository);
        $service->ensureBlockIsValid($block);
        $this->assertTrue(true);
    }

    public function testBlockIsNotValidWhenHashExists(): void
    {
        $repository = $this->createMock(BlockAggregateRepositoryInterface::class);
        $repository->method('findByHash')->willReturn($this->getBlockAggregateWithId('0x'.str_repeat('b', 64)));
        $block = $this->getBlockAggregateWithId('0x'.str_repeat('b', 64));
        $service = new BlockValidationService($repository);
        $this->expectException(DomainException::class);
        $service->ensureBlockIsValid($block);
    }

    /**
     * Helper to create a real BlockAggregate with a given hash.
     *
     * @param string $hash
     *
     * @return BlockAggregate
     */
    private function getBlockAggregateWithId(string $hash): BlockAggregate
    {
        $blockEntity = new BlockEntity(
            1,
            new HashValueObject($hash),
            new DateTimeImmutable('2024-01-01T00:00:00Z'),
            new DateTimeImmutable('2024-01-01T00:00:00Z')
        );

        return new BlockAggregate($blockEntity);
    }
}
