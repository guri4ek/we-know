<?php

namespace App\Tests\Application;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testProcessEnvironmentTakesPrecedenceOverDotenvDefaults(): void
    {
        putenv('APP_ENV=prod');
        putenv('APP_DEBUG=0');
        unset($_ENV['APP_ENV'], $_ENV['APP_DEBUG'], $_SERVER['APP_ENV'], $_SERVER['APP_DEBUG']);

        require dirname(__DIR__, 2).'/config/bootstrap.php';

        self::assertSame('prod', $_SERVER['APP_ENV']);
        self::assertSame('0', $_SERVER['APP_DEBUG']);
    }
}
