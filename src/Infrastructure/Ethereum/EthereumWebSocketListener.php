<?php

declare(strict_types=1);

namespace App\Infrastructure\Ethereum;

use App\Application\DTO\BlockDTO;
use App\Application\Port\EthereumNodeListenerInterface;
use InvalidArgumentException;
use JsonException;

class EthereumWebSocketListener implements EthereumNodeListenerInterface
{
    public function __construct(protected WebSocketDataFetcher $webSocketDataFetcher)
    {
    }

    /**
     * @param callable $handler
     *
     * @throws InvalidArgumentException If date string is malformed
     * @throws JsonException            If JSON data is invalid
     */
    public function onNewBlock(callable $handler): void
    {
        $websocketData = $this->webSocketDataFetcher->fetch();

        $blockDTO = BlockDTO::fromWebSocketData($websocketData);

        $handler($blockDTO);
    }
}
