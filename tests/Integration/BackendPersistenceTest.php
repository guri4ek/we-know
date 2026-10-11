<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Infrastructure\Persistence\ConnectionFactory;
use App\Infrastructure\Persistence\MigrationFactory;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\MigratorConfiguration;
use PHPUnit\Framework\TestCase;

final class BackendPersistenceTest extends TestCase
{
    public static function database(?Connection $connection = null): Connection
    {
        $connection ??= ConnectionFactory::create('sqlite:///:memory:', 'test');
        $factory = MigrationFactory::create($connection);
        $factory->getMetadataStorage()->ensureInitialized();
        $plan = $factory->getMigrationPlanCalculator()->getPlanUntilVersion($factory->getVersionAliasResolver()->resolveVersionAlias('latest'));
        $factory->getMigrator()->migrate($plan, (new MigratorConfiguration())->setAllOrNothing(true));

        return $connection;
    }

    public function testFreshMigrationsAndForeignKeysOnEveryConnection(): void
    {
        $db = self::database();
        self::assertSame(1, (int) $db->fetchOne('PRAGMA foreign_keys'));
        self::assertSame(['answer', 'answer_eligibility', 'doctrine_migration_versions', 'verdict'], $db->createSchemaManager()->listTableNames());
        self::assertSame(1, (int) ConnectionFactory::create('sqlite:///:memory:', 'test')->fetchOne('PRAGMA foreign_keys'));
        $this->expectException(\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException::class);
        $db->insert('answer_eligibility', ['id' => 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa', 'answer_id' => 'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb', 'intent' => 'ANY', 'topic' => 'GENERAL', 'humor_mechanism' => 'metaphor', 'created_at' => '2026-10-11T00:00:00Z']);
    }

    public function testReconnectingStillEnforcesForeignKeys(): void
    {
        $db = ConnectionFactory::create('sqlite:///:memory:', 'test');
        $db->close();
        self::assertSame(1, (int) $db->fetchOne('PRAGMA foreign_keys'));
    }

    public function testUuidPrimaryKeyCannotBeNullInSQLite(): void
    {
        $db = self::database();
        $this->expectException(\Doctrine\DBAL\Exception\NotNullConstraintViolationException::class);
        $db->insert('answer', ['id'=>null, 'text'=>'Synthetic', 'locale'=>'en', 'enabled'=>1, 'created_at'=>'2026-10-11T00:00:00Z']);
    }
}
