<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Ethereum;

use App\Application\DTO\BlockDTO;
use App\Application\Port\EventPublisherInterface;
use App\Application\Service\RegisterBlockService;
use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Entity\BlockEntity;
use App\Domain\Service\BlockValidationService;
use App\Domain\ValueObject\HashValueObject;
use App\Infrastructure\Ethereum\EthereumWebSocketListener;
use App\Infrastructure\Ethereum\WebSocketDataFetcher;
use App\Infrastructure\Repository\DatabaseBlockAggregateRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EthereumWebSocketListenerTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testOnNewBlockCallsHandlerWithBlockDTO(): void
    {
        $mockFetcher = $this->createMock(WebSocketDataFetcher::class);
        $mockFetcher->method('fetch')->willReturn($this->getSampleBlockData());

        $listener = new EthereumWebSocketListener($mockFetcher);

        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(BlockEntity::class);
        assert($repository instanceof DatabaseBlockAggregateRepository);

        $listener->onNewBlock(
            fn (BlockDTO $blockDTO) => $this->registerBlock($blockDTO)
        );

        /** @var DatabaseBlockAggregateRepository $repository */
        $blockAggregate = $repository->findByHash(
            new HashValueObject($this->getSampleBlockData()['hash'])
        );

        $this->assertNotNull($blockAggregate, 'Block was not found in the database.');
        $this->assertSame(26, $blockAggregate->number());
    }

    protected function getSampleBlockData(): array
    {
        return [
            'number' => '0x1a',
            'hash' => '0x'.str_repeat('a', 64),
            'timestamp' => '0x60d5f2',
        ];
    }

    protected function registerBlock(BlockDTO $blockDTO): void
    {
        $registerBlockService = new RegisterBlockService(
            self::getContainer()->get(DatabaseBlockAggregateRepository::class),
            $this->createMock(BlockValidationService::class),
            $this->createMock(EventPublisherInterface::class)
        );

        $blockEntity = new BlockEntity(
            $blockDTO->number,
            new HashValueObject($blockDTO->hash),
            $blockDTO->timestamp,
            $blockDTO->createdAt
        );
        $blockAggregate = new BlockAggregate($blockEntity);
        $registerBlockService->registerBlock($blockAggregate);
    }
}
