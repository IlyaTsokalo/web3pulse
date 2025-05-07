<?php

declare(strict_types=1);

namespace App\Application\Port;

/**
 * Port: Interface for Ethereum node listeners.
 */
interface EthereumNodeListenerInterface
{
    /**
     * @param callable $handler
     *
     * @return void
     */
    public function onNewBlock(callable $handler): void;
}
