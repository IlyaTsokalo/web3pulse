<?php

declare(strict_types=1);

namespace App\Infrastructure\Webhook\Command;

use App\Infrastructure\Webhook\WebhookDeliveryService;
use Exception;
use Predis\Client;
use Predis\Connection\ConnectionException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that subscribes to Redis channels and forwards received events to webhooks.
 */
class WebhookDeliverySubscriberCommand extends Command
{
    /**
     * @param Client                 $redis
     * @param WebhookDeliveryService $delivery
     * @param array                  $channels
     * @param LoggerInterface        $logger
     */
    public function __construct(
        protected readonly Client $redis,
        protected readonly WebhookDeliveryService $delivery,
        protected readonly array $channels,
        protected readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected static $defaultName = 'app:webhook:subscribe';

    protected static $defaultDescription = 'Sends a webhook delivery to the subscriber';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setHelp('This command allows you to send a webhook delivery to the subscriber');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🔔 Starting webhook subscriber…');

        try {
            $this->redis->ping();
            $output->writeln('✓ Connected to Redis');

            $pubsub = $this->redis->pubSubLoop();

            foreach ($this->channels as $channel) {
                $pubsub->subscribe($channel);
                $output->writeln("Subscribed to channel: $channel");
            }

            foreach ($pubsub as $message) {
                if ('message' === $message->kind) {
                    $channel = $message->channel;
                    $payload = $message->payload;

                    $output->writeln("-> [$channel] delivering to webhooks");
                    $this->delivery->deliver($channel, $payload);
                }
            }

            return Command::SUCCESS;
        } catch (ConnectionException $e) {
            $output->writeln('<fg=red>Redis connection failed: '.$e->getMessage().'</>');

            $this->logger->error('Redis connection error in webhook subscriber', [
                'error' => $e->getMessage(),
                'redis_url' => $this->redis->getConnection()->getParameters()->uri,
            ]);

            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln('<fg=red>Error: '.$e->getMessage().'</>');
            $this->logger->error('Webhook subscriber error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
