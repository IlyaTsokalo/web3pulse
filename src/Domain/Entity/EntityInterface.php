<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Interface for all entities, enforcing unique identity and equality.
 */
interface EntityInterface
{
    /**
     * Returns the unique identifier for the entity.
     *
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * Checks identity equality with another entity.
     *
     * @param EntityInterface $other
     *
     * @return bool
     */
    public function equals(EntityInterface $other): bool;
}
