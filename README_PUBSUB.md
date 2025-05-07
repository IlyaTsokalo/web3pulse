# Pub/Sub Process in Web3PulseApp

This document describes the Publish/Subscribe (Pub/Sub) event flow used in the Web3PulseApp, following DDD and hexagonal architecture principles.

## Overview

The application uses Redis Pub/Sub to decouple event production from event consumption. Events (such as new Ethereum blocks) are published to Redis channels, and downstream services or workers can subscribe to these channels to react in real time.
![pubsub.png](pubsub.png)
---

## Architecture Steps (Detailed)

### 1. Define Domain/Application Ports

**EventPublisherInterface**  
- **Location:** `src/Application/Port/EventPublisherInterface.php`
- **Purpose:** Defines the contract for publishing domain/application events (such as new blocks, transactions, or custom events) from your core logic to the outside world.
- **How it works:**
  - Declares a method (`publish(DomainEventInterface $event): void`) that allows the application to publish events without knowing the underlying delivery mechanism (e.g., Redis, message bus, etc).
  - Keeps the application and domain logic decoupled from infrastructure and frameworks.
  - Enables easy swapping or mocking of event publishing in tests or different environments.
- **Why:**
  - Promotes separation of concerns and testability.
  - Makes your application flexible to different event delivery strategies.

**EthereumNodeListenerInterface**  
- **Location:** `src/Application/Port/EthereumNodeListenerInterface.php`
- **Purpose:** Defines the contract for receiving blocks/events from an Ethereum node, abstracting over the details of HTTP, WebSocket, or other protocols.
- **How it works:**
  - Declares methods for subscribing to Ethereum events (e.g., `onNewBlock(callable $handler): void`).
  - Allows your application to react to blockchain data without being tied to a specific node implementation or protocol.
  - The actual connection and event transformation are handled by infrastructure adapters that implement this interface.
- **Why:**
  - Keeps your core logic independent of Ethereum node details.
  - Makes it easy to swap node providers, protocols, or mock blockchain events for testing.

---

### 2. Implement Infrastructure Adapters

**EthereumWebSocketListener (Adapter)**  
- **Implements:** `EthereumNodeListenerInterface` (application port)  
- **Location:** `src/Infrastructure/Ethereum/EthereumWebSocketListener.php`  
- **Purpose:** Bridges your application and the external Ethereum node.  
- **How it works:**
  1. Uses a PHP WebSocket client (like web3p/web3.php or another library) to connect to an Ethereum node’s WebSocket endpoint (e.g., Alchemy).
  2. Subscribes to blockchain events (e.g., new block headers via "newHeads").
  3. When a new block/event is received, it transforms the raw data into your domain event or DTO (e.g., BlockRegisteredEvent).
  4. Calls the handler (provided by your application service) with the transformed event.
- **Why:** Keeps your core logic decoupled from the details of Ethereum/WebSocket communication.

**RedisEventPublisher (Adapter)**  
- **Implements:** `EventPublisherInterface` (application port)  
- **Location:** `src/Infrastructure/EventPublisher/RedisEventPublisher.php`  
- **Purpose:** Bridges your application and Redis for event publishing.  
- **How it works:**
  1. Uses a Redis PHP client (php-redis, predis, or via snc/redis-bundle) to connect to Redis.
  2. When your application calls `publish()`, it serializes the event and publishes it to a Redis channel.
  3. Downstream consumers (e.g., workers, webhooks) can subscribe to these channels to react to events.
- **Why:** Decouples your event publishing from the infrastructure, making it easy to swap or mock in tests.

---

### 3. Application Service Orchestration

**RegisterBlockService (Application Service)**  
- **Location:** `src/Application/Service/RegisterBlockService.php`
- **Purpose:** Orchestrates the processing of new blocks received from the Ethereum node.
- **How it works:**
  1. Exposes a method (e.g., `register(BlockAggregate $block)`) that is called when a new block is detected by the EthereumNodeListenerInterface implementation.
  2. Validates the block data (e.g., checks for duplicates, ensures required fields are present, verifies integrity).
  3. Persists the block using a repository interface (e.g., `BlockAggregateRepositoryInterface`), which abstracts the underlying database or storage mechanism.
  4. Creates a `BlockRegisteredEvent` (implements `DomainEventInterface`) with relevant block information.
  5. Publishes the event using `EventPublisherInterface`, allowing infrastructure adapters (e.g., RedisEventPublisher) to distribute the event to downstream consumers.
- **Why:**
  - Centralizes block processing logic in the application layer, keeping domain and infrastructure concerns separated.
  - Ensures all necessary steps (validation, persistence, event publishing) are performed in a consistent and testable way.
  - Makes it easy to extend or modify block processing without touching lower-level infrastructure or domain logic.

---

### 4. Event Consumption (Downstream)

**WebhookDeliveryService (Infrastructure Adapter)**  
- **Location:** `src/Infrastructure/Webhook/WebhookDeliveryService.php`  
- **Purpose:** Takes events from Redis channels and delivers them as HTTP webhooks to registered endpoints.
- **How it works:**
  1. Receives a channel name and payload from the Redis subscriber.
  2. Looks up registered webhook URLs for that channel in a configuration map.
  3. Delivers the payload via HTTP POST to each registered endpoint.
  4. Handles errors and logging for failed deliveries.
- **Why:**
  - Provides a clean, focused service for webhook delivery.
  - Follows single responsibility principle by separating event consumption from delivery logic.

**WebhookDeliverySubscriberCommand (Infrastructure/Worker)**  
- **Location:** `src/Infrastructure/Webhook/Command/WebhookDeliverySubscriberCommand.php`  
- **Purpose:** Consumes events published to Redis channels and forwards them to the WebhookDeliveryService.
- **How it works:**
  1. Runs as a long-running Symfony command in a dedicated Kubernetes pod.
  2. Connects to Redis and subscribes to configured channels.
  3. When an event is received, passes it to the WebhookDeliveryService for delivery.
  4. Handles Redis connection issues and provides proper error reporting.
- **Why:**
  - Decouples event consumption from the main application.
  - Runs as a separate process, allowing independent scaling and fault tolerance.
  - Follows the hexagonal architecture pattern by keeping infrastructure concerns isolated.

**Kubernetes Deployment**
- **Location:** `k8s/deployments/webhook-subscriber-deployment.yaml`
- **Purpose:** Runs the webhook subscriber as a dedicated service in the Kubernetes cluster.
- **How it works:**
  1. Creates a pod that runs the webhook subscriber command.
  2. Connects directly to the Redis service within the cluster.
  3. Automatically restarts if the process crashes.
  4. Mounts the application code for execution.
- **Why:**
  - Provides a production-ready deployment for the webhook delivery system.
  - Follows infrastructure-as-code principles.
  - Ensures the webhook subscriber is always running and connected to Redis.

**Configuration**
- **Webhook Configuration:** `config/packages/webhook.yaml`
- **Redis Configuration:** `config/packages/snc_redis.yaml`
- **Service Wiring:** `config/services.yaml`
- **How it works:**
  1. The webhook configuration defines which channels to listen to and which URLs to deliver to.
  2. The Redis configuration sets up the connection to the Redis service.
  3. The service wiring connects the WebhookDeliveryService and WebhookDeliverySubscriberCommand.

---
