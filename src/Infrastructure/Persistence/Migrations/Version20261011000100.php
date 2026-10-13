<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261011000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'English answer catalog, explicit eligibilities and immutable verdict snapshots';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();
        $this->abortIf(!$platform instanceof SQLitePlatform && !$platform instanceof PostgreSQLPlatform, 'Unsupported database platform.');
        $boolean = $platform instanceof PostgreSQLPlatform ? 'BOOLEAN' : 'INTEGER CHECK (enabled IN (0, 1))';
        $json = $platform instanceof PostgreSQLPlatform ? 'JSONB' : 'TEXT CHECK (json_valid(decision_context))';
        $this->addSql("CREATE TABLE answer (id VARCHAR(36) NOT NULL PRIMARY KEY, text TEXT NOT NULL CHECK (length(text) > 0), locale VARCHAR(8) NOT NULL, enabled $boolean NOT NULL, created_at VARCHAR(20) NOT NULL)");
        $this->addSql("CREATE TABLE answer_eligibility (id VARCHAR(36) NOT NULL PRIMARY KEY, answer_id VARCHAR(36) NOT NULL REFERENCES answer(id) ON DELETE CASCADE, intent VARCHAR(16) NOT NULL CHECK (intent IN ('YES_NO','WHEN','DURATION','EXPLAIN','METHOD','WHO','IDENTIFY','DESCRIBE','WHERE','QUANTITY','CHOICE','RECOMMEND','COMPARE','OTHER','ANY')), topic VARCHAR(16) NOT NULL CHECK (topic IN ('TECH','WORK_STUDY','MONEY','RELATIONSHIPS','HEALTH','FOOD','HOME','NATURE','TRAVEL','SPORTS','ENTERTAINMENT','LANGUAGE','GENERAL')), humor_mechanism VARCHAR(32) NOT NULL CHECK (humor_mechanism IN ('metaphor','hyperbole','oxymoron','paradox','false_causality','false_authority','reversal','bureaucratic_absurdity')), created_at VARCHAR(20) NOT NULL, UNIQUE (answer_id, intent, topic), CHECK (intent <> 'ANY' OR topic = 'GENERAL'))");
        $this->addSql('CREATE INDEX eligibility_pool ON answer_eligibility (intent, topic, answer_id)');
        $this->addSql("CREATE TABLE verdict (id VARCHAR(36) NOT NULL PRIMARY KEY, question_snapshot TEXT NOT NULL, answer_snapshot TEXT NOT NULL, answer_eligibility_id VARCHAR(36) DEFAULT NULL REFERENCES answer_eligibility(id) ON DELETE SET NULL, decision_context $json NOT NULL, created_at VARCHAR(20) NOT NULL)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE verdict');
        $this->addSql('DROP TABLE answer_eligibility');
        $this->addSql('DROP TABLE answer');
    }
}
