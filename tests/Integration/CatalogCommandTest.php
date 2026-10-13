<?php

declare(strict_types=1);
namespace App\Tests\Integration;

use App\Command\ImportCatalogCommand;
use App\Infrastructure\Persistence\CatalogImporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CatalogCommandTest extends TestCase
{
    public function testDevelopmentFixtureIsExplicitAndCannotLoadInProduction(): void
    {
        $db = BackendPersistenceTest::database();
        $tester = new CommandTester(new ImportCatalogCommand(new CatalogImporter($db), dirname(__DIR__, 2), 'dev'));
        self::assertSame(0, $tester->execute(['--development-fixture' => true]));
        self::assertSame(5, (int) $db->fetchOne('SELECT COUNT(*) FROM answer'));
        self::assertSame(2, $tester->execute(['file' => __DIR__.'/../Fixtures/Catalog/development-v2.json']));
        $production = new CommandTester(new ImportCatalogCommand(new CatalogImporter($db), dirname(__DIR__, 2), 'prod'));
        self::assertSame(2, $production->execute(['--development-fixture' => true]));
    }

    public function testInvalidJsonFileReturnsControlledFailure(): void
    {
        $db = BackendPersistenceTest::database();
        $tester = new CommandTester(new ImportCatalogCommand(new CatalogImporter($db), dirname(__DIR__, 2), 'test'));
        self::assertSame(1, $tester->execute(['file' => dirname(__DIR__, 2).'/composer.json']));
        self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM answer'));
        self::assertStringContainsString('Invalid catalog', $tester->getDisplay());
    }
}
