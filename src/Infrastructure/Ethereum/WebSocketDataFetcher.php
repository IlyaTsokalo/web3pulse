<?php

declare(strict_types=1);

namespace App\Infrastructure\Ethereum;

use JsonException;
use LogicException;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use React\EventLoop\Loop;
use React\Socket\Connector as ReactConnector;
use Throwable;

class WebSocketDataFetcher
{
    protected const string WEBSOCKET_URL = 'wss://eth-mainnet.g.alchemy.com/v2/GM2Wr1XStrrmFZmydMx2X-zaVOWyUmTc';

    protected const array SUBSCRIPTION_PAYLOAD = [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'eth_subscribe',
        'params' => ['newHeads'],
    ];

    /**
     * @throws JsonException
     *
     * @return array
     */
    public function fetch(): array
    {
        $loop = Loop::get();
        $reactConnector = new ReactConnector([
            'dns' => '8.8.8.8',
            'timeout' => 10,
        ]);
        $connector = new Connector($loop, $reactConnector);
        $webSocketData = null;

        $connector(self::WEBSOCKET_URL)
            ->then(function (WebSocket $connection) use ($loop): void {
                // subscription
                $connection->send(json_encode(self::SUBSCRIPTION_PAYLOAD, JSON_THROW_ON_ERROR));

                $connection->on('message', function ($msg) use (&$webSocketData): void {
                    $data = json_decode((string) $msg, true, 512, JSON_THROW_ON_ERROR);

                    if (
                        isset($data['method'], $data['params']['result'])
                        && 'eth_subscription' === $data['method']
                    ) {
                        $webSocketData = $data['params']['result'];
                    }
                });

                $connection->on('close', function ($code = null, $reason = null) use ($loop): void {
                    echo "Connection closed ($code - $reason)\n";
                    $loop->stop();
                });
            }, function (Throwable $e) use ($loop): void {
                $loop->stop();

                $message = sprintf('Could not connect to WebSocket: %s', $e->getMessage());
                throw new LogicException($message);
            });

        $loop->run();

        if (null === $webSocketData) {
            throw new LogicException('No data received from WebSocket.');
        }

        return json_decode($webSocketData, true, 512, JSON_THROW_ON_ERROR);
    }
}
