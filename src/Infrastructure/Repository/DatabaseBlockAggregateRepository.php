<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Aggregate\BlockAggregate;
use App\Domain\Entity\BlockEntity;
use App\Domain\Repository\BlockAggregateRepositoryInterface;
use App\Domain\ValueObject\HashValueObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Adapter: Database-backed implementation of BlockAggregateRepositoryInterface.
 *
 * NOTE: This implementation uses Doctrine ORM.
 */
class DatabaseBlockAggregateRepository extends ServiceEntityRepository implements BlockAggregateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockEntity::class);
    }

    /**
     * @param BlockAggregate $block
     *
     * @return void
     */
    public function save(BlockAggregate $block): void
    {
        $blockEntity = $block->getPersistenceRoot();

        $this->getEntityManager()->persist($blockEntity);

        $this->getEntityManager()->flush();
    }

    /**
     * @param HashValueObject $hash
     *
     * @return BlockAggregate|null
     */
    public function findByHash(HashValueObject $hash): ?BlockAggregate
    {
        $blockEntity = $this->getEntityManager()
            ->getRepository(BlockEntity::class)
            ->findOneBy(['hashValue' => $hash->value()]);

        if (!$blockEntity) {
            return null;
        }

        return BlockAggregate::reconstitute($blockEntity);
    }
}
