<?php

declare(strict_types=1);
namespace App\Tests\Unit;

use App\Http\BackendConsoleErrorSubscriber;
use App\Infrastructure\Persistence\ConnectionFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class BackendErrorTest extends TestCase
{
    public function testInvalidDatabaseConfigurationNeverLeaksTheDsn(): void
    {
        try {
            ConnectionFactory::create('postgresql://synthetic:secret@/invalid', 'prod');
            self::fail('Invalid DSN must fail');
        } catch (\Throwable $error) {
            self::assertStringNotContainsString('secret', $error->getMessage());
            self::assertNull($error->getPrevious());
        }
    }

    public function testMigrationConsoleErrorOmitsDatabaseDetails(): void
    {
        $event = new ConsoleErrorEvent(new ArrayInput([]), new BufferedOutput(), new \RuntimeException('synthetic password DSN'), new Command('migrations:migrate'));
        (new BackendConsoleErrorSubscriber())->onError($event);
        self::assertSame('Database operation failed. Check database configuration and availability.', $event->getError()->getMessage());
        self::assertNull($event->getError()->getPrevious());
    }
}
