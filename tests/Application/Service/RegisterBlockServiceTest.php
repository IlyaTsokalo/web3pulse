<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\DTO\BlockDTO;
use App\Application\Port\EventPublisherInterface;
use App\Application\Service\RegisterBlockService;
use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Entity\BlockEntity;
use App\Domain\Repository\BlockAggregateRepositoryInterface;
use App\Domain\Service\BlockValidationService;
use App\Domain\ValueObject\HashValueObject;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @covers \RegisterBlockService
 */
class RegisterBlockServiceTest extends KernelTestCase
{
    use ResetDatabase;

    public function testRegisterBlockCallsRepositorySave(): void
    {
        $repository = $this->createMock(BlockAggregateRepositoryInterface::class);
        $blockValidationService = $this->createMock(BlockValidationService::class);
        $eventPublisher = $this->createMock(EventPublisherInterface::class);

        $blockEntity = new BlockEntity(
            1,
            new HashValueObject('0x'.str_repeat('a', 64)),
            new DateTimeImmutable('2024-01-01T00:00:00Z'),
            new DateTimeImmutable('@0'),
            new DateTimeImmutable('2024-01-01T00:00:00Z')
        );
        $block = new BlockAggregate($blockEntity);

        $blockValidationService->expects($this->once())
            ->method('ensureBlockIsValid')
            ->with($block);
        $repository->expects($this->once())
            ->method('save')
            ->with($block);

        $service = new RegisterBlockService($repository, $blockValidationService, $eventPublisher);
        $service->registerBlock($block);
    }

    public function testBlockDTO(): void
    {
        $blockEntity = new BlockEntity(
            1,
            new HashValueObject('0x'.str_repeat('a', 64)),
            new DateTimeImmutable('2024-01-01T00:00:00Z'),
            new DateTimeImmutable('@0'),
            new DateTimeImmutable('2024-01-01T00:00:00Z')
        );
        $block = new BlockAggregate($blockEntity);

        $dto = BlockDTO::fromDomain($block);
        $this->assertSame(1, $dto->number);
        $this->assertSame('0x'.str_repeat('a', 64), $dto->hash);
    }

    public function testRegisterBlockValidatesAndSavesBlock(): void
    {
        $repository = $this->createMock(BlockAggregateRepositoryInterface::class);
        $blockValidationService = $this->createMock(BlockValidationService::class);
        $eventPublisher = $this->createMock(EventPublisherInterface::class);

        $blockEntity = new BlockEntity(
            2,
            new HashValueObject('0x'.str_repeat('b', 64)),
            new DateTimeImmutable('2024-01-02T00:00:00Z'),
            new DateTimeImmutable('@0'),
        );
        $block = new BlockAggregate($blockEntity);

        $blockValidationService->expects($this->once())
            ->method('ensureBlockIsValid')
            ->with($block);
        $repository->expects($this->once())->method('save')->with($block);

        $service = new RegisterBlockService($repository, $blockValidationService, $eventPublisher);
        $service->registerBlock($block);
    }

    public function testRegisterBlockThrowsOnInvalidBlock(): void
    {
        $repository = $this->createMock(BlockAggregateRepositoryInterface::class);
        $blockValidationService = $this->createMock(BlockValidationService::class);
        $eventPublisher = $this->createMock(EventPublisherInterface::class);

        $blockEntity = new BlockEntity(
            3,
            new HashValueObject('0x'.str_repeat('c', 64)),
            new DateTimeImmutable('2024-01-03T00:00:00Z'),
            new DateTimeImmutable('@0'),
            new DateTimeImmutable('2024-01-03T00:00:00Z')
        );
        $block = new BlockAggregate($blockEntity);

        $blockValidationService->expects($this->once())
            ->method('ensureBlockIsValid')
            ->with($block)
            ->willThrowException(new DomainException('Block is not valid!'));
        $repository->expects($this->never())->method('save');

        $service = new RegisterBlockService($repository, $blockValidationService, $eventPublisher);
        $this->expectException(DomainException::class);
        $service->registerBlock($block);
    }
}
