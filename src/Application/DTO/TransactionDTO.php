<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\TransactionEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @property string                    $hash
 * @property string                    $from
 * @property string                    $to
 * @property string                    $value
 * @property Collection<int, EventDTO> $events
 */
class TransactionDTO
{
    /**
     * @param string                    $hash
     * @param string                    $from
     * @param string                    $to
     * @param string                    $value
     * @param Collection<int, EventDTO> $events
     */
    public function __construct(
        public string $hash,
        public string $from,
        public string $to,
        public string $value,
        public Collection $events
    ) {
        if (!$events instanceof Collection) {
            $this->events = new ArrayCollection($events);
        }
    }

    /**
     * Create TransactionDTO from TransactionEntity.
     *
     * @param TransactionEntity $transaction
     *
     * @return static
     */
    public static function fromDomain(TransactionEntity $transaction): self
    {
        $events = array_map(
            fn ($event) => EventDTO::fromDomain($event),
            $transaction->events()
        );

        return new self(
            $transaction->hash()->value(),
            $transaction->from()->value(),
            $transaction->to()->value(),
            $transaction->value()->value(),
            new ArrayCollection($events)
        );
    }
}
