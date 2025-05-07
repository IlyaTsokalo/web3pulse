<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Entity\BlockEntity;
use App\Domain\Repository\BlockAggregateRepositoryInterface;
use App\Domain\ValueObject\HashValueObject;
use App\Infrastructure\Repository\DatabaseBlockAggregateRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Integration test for the DatabaseBlockAggregateRepository adapter.
 */
class DatabaseBlockAggregateRepositoryTest extends KernelTestCase
{
    use ResetDatabase;

    private EntityManagerInterface $entityManager;
    private BlockAggregateRepositoryInterface $repository;

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $this->entityManager->getRepository(BlockEntity::class);
        assert($this->repository instanceof DatabaseBlockAggregateRepository);
        $this->truncateTable('blocks');
    }

    /**
     * @param string $table
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    private function truncateTable(string $table): void
    {
        $conn = $this->entityManager->getConnection();

        $platform = $conn->getDatabasePlatform();

        $conn->executeStatement($platform->getTruncateTableSQL($table, true));
    }

    /**
     * It saves and finds a BlockAggregate by hash using the adapter.
     *
     * @return void
     */
    public function testSaveAndFindByHash(): void
    {
        // Arrange
        $hash = new HashValueObject('0x'.str_repeat('a', 64));

        $blockEntity = new BlockEntity(
            1,
            $hash,
            new DateTimeImmutable('2024-01-01T00:00:00Z'),
            new DateTimeImmutable('2024-01-01T00:00:00Z')
        );
        $aggregate = new BlockAggregate($blockEntity);

        // Act
        $this->repository->save($aggregate);

        $found = $this->repository->findByHash($hash);

        // Assert
        $this->assertInstanceOf(BlockAggregate::class, $found);
        $this->assertSame(
            $aggregate->getPersistenceRoot()->hash(),
            $found->getPersistenceRoot()->hash()
        );
    }
}
