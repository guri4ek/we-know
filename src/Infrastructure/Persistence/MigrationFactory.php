<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;

final class MigrationFactory
{
    public static function create(Connection $connection): DependencyFactory
    {
        return DependencyFactory::fromConnection(new ConfigurationArray([
            'migrations_paths' => ['App\Infrastructure\Persistence\Migrations' => __DIR__.'/Migrations'],
            'all_or_nothing' => true,
            'check_database_platform' => true,
        ]), new ExistingConnection($connection));
    }
}
