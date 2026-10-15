<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\MigratorConfiguration;

/** Only the disposable local development database; never an upgrade tool. */
final class LocalDatabase
{
    private ?Connection $connection = null;

    public function __construct(
        private readonly string $projectDir,
        private readonly string $environment,
        #[\SensitiveParameter] private readonly string $databaseUrl,
    ) {}

    public function reset(): void
    {
        $path = $this->target();
        $this->connection?->close();
        $this->connection = null;
        // Closing SQLite can remove sidecars. Recheck the entire set before deleting any file.
        $this->target();
        foreach (['-wal', '-shm', '-journal', ''] as $suffix) {
            $file = $path.$suffix;
            if (file_exists($file) && !unlink($file)) {
                throw new \RuntimeException('Local database reset failed.');
            }
        }
    }

    public function migrate(): void
    {
        $db = $this->connect();
        try {
            $factory = MigrationFactory::create($db);
            $factory->getMetadataStorage()->ensureInitialized();
            $plan = $factory->getMigrationPlanCalculator()->getPlanUntilVersion($factory->getVersionAliasResolver()->resolveVersionAlias('latest'));
            $factory->getMigrator()->migrate($plan, (new MigratorConfiguration())->setAllOrNothing(true));
        } finally {
            $db->close();
        }
    }

    public function seed(): void
    {
        $db = $this->connect();
        try {
            $json = @file_get_contents($this->projectDir.'/docs/content/seed-v2/catalog.json');
            if ($json === false) {
                throw new \RuntimeException('Canonical editorial catalog is not readable.');
            }
            (new CatalogImporter($db))->import($json);
        } finally {
            $db->close();
        }
    }

    private function connect(): Connection
    {
        $this->target();
        return $this->connection ??= ConnectionFactory::create($this->databaseUrl, $this->environment);
    }

    private function target(): string
    {
        $root = realpath($this->projectDir);
        $path = $root.'/var/we-know.sqlite';
        // Exact DSN comparison also rejects hosts, query options, traversal, and URI modes.
        if ($this->environment !== 'dev' || $root === false || $this->databaseUrl !== 'sqlite:///'.$path) {
            throw new \RuntimeException('Database lifecycle requires APP_ENV=dev and the default local SQLite target.');
        }
        clearstatcache();
        if (is_link($root.'/var') || realpath($root.'/var') !== $root.'/var') {
            throw new \RuntimeException('The local var directory must exist and must not be a symlink.');
        }
        foreach (['', '-wal', '-shm', '-journal'] as $suffix) {
            $file = $path.$suffix;
            $stat = @lstat($file);
            if (is_link($file) || ($stat !== false && (!is_file($file) || $stat['nlink'] !== 1))) {
                throw new \RuntimeException('Database and sidecars must be regular unlinked local files.');
            }
        }
        return $path;
    }
}
