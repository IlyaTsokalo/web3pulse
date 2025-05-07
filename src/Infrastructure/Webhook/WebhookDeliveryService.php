<?php

declare(strict_types=1);

namespace App\Infrastructure\Webhook;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * Takes a channel name and raw JSON payload, and fires HTTP POSTs.
 */
class WebhookDeliveryService
{
    /**
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface     $logger
     * @param array               $webhookMap
     */
    public function __construct(
        protected readonly HttpClientInterface $httpClient,
        protected readonly LoggerInterface $logger,
        protected readonly array $webhookMap
    ) {
    }

    /**
     * @param string $channelName
     * @param string $payload
     *
     * @return void
     */
    public function deliver(string $channelName, string $payload): void
    {
        $urls = $this->webhookMap[$channelName] ?? [];
        foreach ($urls as $url) {
            try {
                $this->httpClient->request('POST', $url, [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => $payload,
                ]);
            } catch (Throwable $e) {
                $this->logger->error('Webhook POST failed', [
                    'channel' => $channelName,
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
