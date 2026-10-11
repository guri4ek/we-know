<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;

final class ConnectionFactory
{
    public static function create(#[\SensitiveParameter] string $url, string $environment): Connection
    {
        $params = (new DsnParser(['sqlite' => 'pdo_sqlite', 'postgresql' => 'pdo_pgsql']))->parse($url);
        if (!in_array($params['driver'] ?? '', ['pdo_sqlite', 'pdo_pgsql'], true)) {
            throw new \InvalidArgumentException('Only SQLite and PostgreSQL are supported.');
        }
        if ($environment === 'prod' && $params['driver'] !== 'pdo_pgsql') {
            throw new \InvalidArgumentException('Production requires PostgreSQL.');
        }
        $config = new Configuration();
        if ($params['driver'] === 'pdo_sqlite') {
            $config->setMiddlewares([new SQLiteConnectionMiddleware()]);
        }
        $db = DriverManager::getConnection($params, $config);

        return $db;
    }
}
