<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Infrastructure\Persistence\{ConnectionFactory, LocalDatabase};
use App\Command\LocalDatabaseCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class LocalDatabaseTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir().'/we-know-lifecycle-'.bin2hex(random_bytes(8));
        mkdir($this->root.'/var', 0700, true);
        $this->root = realpath($this->root);
        mkdir($this->root.'/docs/content/seed-v2', 0700, true);
        copy(dirname(__DIR__, 2).'/docs/content/seed-v2/catalog.json', $this->root.'/docs/content/seed-v2/catalog.json');
    }

    protected function tearDown(): void
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->root, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            $file->isDir() && !$file->isLink() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($this->root);
    }

    private function lifecycle(string $environment = 'dev', ?string $url = null): LocalDatabase
    {
        return new LocalDatabase($this->root, $environment, $url ?? 'sqlite:///'.$this->root.'/var/we-know.sqlite');
    }

    public function testAbsentDatabaseAndRepeatedSeed(): void
    {
        $local = $this->lifecycle();
        $local->reset();
        $local->migrate();
        $local->seed();
        $db = ConnectionFactory::create('sqlite:///'.$this->root.'/var/we-know.sqlite', 'dev');
        $before = $db->fetchAllAssociative('SELECT * FROM answer ORDER BY id');
        $pairs = $db->fetchAllAssociative('SELECT * FROM answer_eligibility ORDER BY id');
        $local->seed();
        self::assertSame($before, $db->fetchAllAssociative('SELECT * FROM answer ORDER BY id'));
        self::assertSame($pairs, $db->fetchAllAssociative('SELECT * FROM answer_eligibility ORDER BY id'));
        $catalog = json_decode(file_get_contents($this->root.'/docs/content/seed-v2/catalog.json'), true, flags: JSON_THROW_ON_ERROR);
        self::assertCount(count($catalog['answers']), $before);
        self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
        self::assertSame('ok', $db->fetchOne('PRAGMA integrity_check'));
        self::assertSame([], $db->fetchAllAssociative('PRAGMA foreign_key_check'));
        $db->close();
    }

    public function testOutdatedDatabaseAndSidecarsAreRemovedButDemoIsPreserved(): void
    {
        $path = $this->root.'/var/we-know.sqlite';
        $db = ConnectionFactory::create('sqlite:///'.$path, 'dev');
        $db->executeStatement('CREATE TABLE obsolete (value TEXT)');
        $db->executeStatement('CREATE TABLE doctrine_migration_versions (version TEXT)');
        $db->close();
        foreach (['-wal', '-shm', '-journal'] as $suffix) { file_put_contents($path.$suffix, 'stale'); }
        file_put_contents($this->root.'/var/answer-seed-v2-preserved.sqlite', 'preserve');
        $local = $this->lifecycle();
        $local->reset();
        foreach (['', '-wal', '-shm', '-journal'] as $suffix) { self::assertFileDoesNotExist($path.$suffix); }
        $local->migrate();
        $local->seed();
        self::assertSame(['answer', 'answer_eligibility', 'doctrine_migration_versions', 'verdict'], $db->createSchemaManager()->listTableNames());
        self::assertSame('preserve', file_get_contents($this->root.'/var/answer-seed-v2-preserved.sqlite'));
        $db->close();
        // The lifecycle closes its own connections before reset/reuse.
        $local->reset();
        $local->migrate();
        $local->seed();
    }

    #[DataProvider('unsafeTargets')]
    public function testTargetRestrictions(string $case): void
    {
        $path = $this->root.'/var/we-know.sqlite';
        file_put_contents($path, 'preserve');
        $url = 'sqlite:///'.$path;
        $environment = 'dev';
        switch ($case) {
            case 'prod': $environment = 'prod'; break;
            case 'test': $environment = 'test'; break;
            case 'remote': $url = 'postgresql://localhost/example'; break;
            case 'host': $url = 'sqlite://remote'.$path; break;
            case 'memory': $url = 'sqlite:///:memory:'; break;
            case 'unexpected': $url = 'sqlite:///'.$this->root.'/var/answer-seed-v2-preserved.sqlite'; break;
            case 'traversal': $url = 'sqlite:///'.$this->root.'/var/../var/we-know.sqlite'; break;
            case 'query': $url .= '?mode=memory'; break;
            case 'symlink': rename($path, $this->root.'/original'); symlink($this->root.'/original', $path); break;
            case 'sidecar': file_put_contents($this->root.'/original', 'preserve'); symlink($this->root.'/original', $path.'-wal'); break;
            case 'dangling': symlink($this->root.'/missing', $path.'-shm'); break;
            case 'directory': mkdir($path.'-journal'); break;
            case 'parent': rename($this->root.'/var', $this->root.'/elsewhere'); symlink($this->root.'/elsewhere', $this->root.'/var'); break;
            case 'hardlink': link($path, $this->root.'/original'); break;
        }
        try {
            $this->lifecycle($environment, $url)->reset();
            self::fail('Unsafe target accepted');
        } catch (\RuntimeException) {
            self::assertSame('preserve', file_get_contents($path));
        }
    }

    public static function unsafeTargets(): iterable
    {
        foreach (['prod', 'test', 'remote', 'host', 'memory', 'unexpected', 'traversal', 'query', 'symlink', 'sidecar', 'dangling', 'directory', 'parent', 'hardlink'] as $case) { yield $case => [$case]; }
    }

    #[DataProvider('failureSteps')]
    public function testComposerFreshStopsAtFirstFailure(int $failure): void
    {
        $scripts = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true)['scripts'] ?? [];
        self::assertArrayHasKey('db:fresh', $scripts);
        foreach (['reset', 'migrate', 'seed'] as $index => $action) {
            file_put_contents($this->root.'/'.$action.'.php', '<?php file_put_contents(__DIR__."/steps", "'.$action.'\n", FILE_APPEND); exit('.($failure === $index ? 7 : 0).');');
            $scripts['db:'.$action] = '@php '.$action.'.php';
        }
        file_put_contents($this->root.'/composer.json', json_encode(['scripts' => $scripts], JSON_THROW_ON_ERROR));
        $process = proc_open(['composer', '--no-plugins', '--no-interaction', 'db:fresh'], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $this->root);
        fclose($pipes[0]); stream_get_contents($pipes[1]); fclose($pipes[1]); stream_get_contents($pipes[2]); fclose($pipes[2]);
        self::assertNotSame(0, proc_close($process));
        self::assertSame(array_slice(['reset', 'migrate', 'seed'], 0, $failure + 1), file($this->root.'/steps', FILE_IGNORE_NEW_LINES));
    }

    public static function failureSteps(): iterable { yield [0]; yield [1]; }

    public function testConsoleActionsAndFailuresDoNotDiscloseConnectionDetails(): void
    {
        $tester = new CommandTester(new LocalDatabaseCommand($this->lifecycle()));
        self::assertSame(2, $tester->execute(['action' => 'unknown']));
        foreach (['reset', 'migrate', 'seed'] as $action) {
            self::assertSame(0, $tester->execute(['action' => $action]));
            self::assertStringContainsString('completed', $tester->getDisplay());
        }
        $rejected = new CommandTester(new LocalDatabaseCommand($this->lifecycle('prod', 'postgresql://unused:synthetic-marker@invalid/database')));
        foreach (['reset', 'migrate', 'seed'] as $action) {
            self::assertSame(1, $rejected->execute(['action' => $action]));
            self::assertStringNotContainsString('synthetic-marker', $rejected->getDisplay());
        }
    }
}
