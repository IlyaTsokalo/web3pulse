<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418174457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE blocks (number BIGINT NOT NULL, hash VARCHAR(66) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, transactions_count INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(number))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE events (log_index_value VARCHAR(255) NOT NULL, address_value VARCHAR(255) NOT NULL, event_name VARCHAR(255) NOT NULL, parameters JSON NOT NULL, PRIMARY KEY(log_index_value))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transactions (hash_value VARCHAR(255) NOT NULL, from_address VARCHAR(255) NOT NULL, to_address VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(hash_value))
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE blocks
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE events
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transactions
        SQL);
    }
}
