# Web3PulseApp Ubiquitous Language & Terminology

This document defines the core terminology and concepts used throughout the Web3PulseApp project, following Domain-Driven Design principles.

## Core Concepts

- **Block**: A collection of transactions included in the Ethereum blockchain. Identified by a unique number and hash.
- **Transaction**: An individual operation on the blockchain, such as a transfer or contract call. Identified by a unique hash.
- **Event (Log)**: Data emitted by smart contracts during transaction execution, used for tracking contract activity.
- **Address**: A unique identifier for an Ethereum account or contract (e.g., `0x...`).
- **Hash**: A fixed-length string (usually hex) used to uniquely identify blocks, transactions, and other data.
- **Wei**: The smallest denomination of Ether (1 ETH = 10^18 Wei).
- **Subscription**: A user's registration to receive notifications about specific blockchain events (e.g., new blocks, transfers).
- **Webhook**: An HTTP callback triggered by the app to notify subscribers about blockchain events.
- **ABI (Application Binary Interface)**: The interface specification for calling and decoding smart contract functions and events.
- **Node**: An Ethereum node (e.g., Geth, Erigon, Infura) that provides blockchain data and event streams.

## Application Roles

- **API Consumer**: An external system or user querying blockchain data via the app's REST API.
- **Webhook Subscriber**: An external system registered to receive webhook notifications for specific blockchain events.

## Technical Terms

- **Port**: An interface in the domain layer describing a dependency on an external system (e.g., repository, event publisher).
- **Adapter**: An infrastructure implementation of a port (e.g., database adapter, webhook publisher).
- **Domain Service**: A stateless service encapsulating domain logic that doesn't naturally fit within an entity or value object.
- **Value Object**: An immutable object representing a concept with no identity (e.g., Address, Hash, Wei).
- **Entity**: An object with a distinct identity that persists over time (e.g., Block, Transaction, Event).

---

_This file should be updated as the model and language evolve._
