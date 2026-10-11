<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Infrastructure\Persistence\CatalogImporter;
use App\Infrastructure\Persistence\CatalogRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CatalogImportTest extends TestCase
{
    public static function fixture(): array
    {
        return json_decode(file_get_contents(__DIR__.'/../Fixtures/Catalog/development-v1.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    public function testImportIsIdempotentAndPairsAreExplicit(): void
    {
        $db = BackendPersistenceTest::database();
        $importer = new CatalogImporter($db);
        $data = self::fixture();
        $data['answers'][0]['text'] = 'Unicode 🦉 <b>snapshot</b>';
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        self::assertSame(5, (int) $db->fetchOne('SELECT COUNT(*) FROM answer'));
        self::assertSame(6, (int) $db->fetchOne('SELECT COUNT(*) FROM answer_eligibility'));
        self::assertSame('2026-10-11T00:00:00Z', $db->fetchOne('SELECT created_at FROM answer LIMIT 1'));
        $catalog = new CatalogRepository($db);
        self::assertCount(0, $catalog->pool('WHY', 'TECH'));
        self::assertCount(1, $catalog->pool('WHY', 'WORK'));
        self::assertCount(2, $catalog->pool('WHEN', 'TECH'));
        self::assertSame($data['answers'][0]['text'], $catalog->pool('WHY', 'WORK')[0]->answer->text);
        $db->update('answer', ['enabled' => false], ['id' => $data['answers'][0]['id']], ['enabled' => \Doctrine\DBAL\Types\Types::BOOLEAN]);
        $db->update('answer', ['locale' => 'uk'], ['id' => $data['answers'][1]['id']]);
        self::assertSame([], $catalog->pool('WHEN', 'TECH'));
    }

    #[DataProvider('invalidCatalogs')]
    public function testInvalidRecordLeavesExistingCatalogUntouched(string $case): void
    {
        $db = BackendPersistenceTest::database();
        $importer = new CatalogImporter($db);
        $original = self::fixture();
        $importer->import(json_encode($original, JSON_THROW_ON_ERROR));
        $data = $original;
        $data['answers'][0]['text'] = 'Must not persist';
        switch ($case) {
            case 'version': $data['version'] = 2; break;
            case 'uuid': $data['answers'][1]['id'] = 'invalid'; break;
            case 'time': $data['answers'][1]['created_at'] = '2026-02-30T00:00:00Z'; break;
            case 'offset': $data['answers'][1]['created_at'] = '2026-10-11T01:00:00+01:00'; break;
            case 'empty': $data['answers'][1]['text'] = '  '; break;
            case 'nul': $data['answers'][1]['text'] = "bad\0text"; break;
            case 'locale': $data['answers'][1]['locale'] = 'uk'; break;
            case 'enabled': $data['answers'][1]['enabled'] = 1; break;
            case 'intent': $data['answers'][1]['eligibility'][0]['intent'] = 'NOPE'; break;
            case 'topic': $data['answers'][1]['eligibility'][0]['topic'] = 'ANY'; break;
            case 'any_topic': $data['answers'][4]['eligibility'][0]['topic'] = 'TECH'; break;
            case 'mechanism': $data['answers'][1]['eligibility'][0]['humor_mechanism'] = 'false_analogy'; break;
            case 'duplicate_pair': $row = $data['answers'][1]['eligibility'][0]; $row['id'] = '20000000-0000-4000-8000-000000000099'; $row['humor_mechanism'] = 'hyperbole'; $data['answers'][1]['eligibility'][] = $row; break;
            case 'duplicate_answer': $data['answers'][] = $data['answers'][0]; break;
            case 'duplicate_eligibility': $data['answers'][1]['eligibility'][0]['id'] = $data['answers'][0]['eligibility'][0]['id']; break;
            case 'no_universal': $data['answers'][4]['enabled'] = false; break;
            case 'unknown': $data['answers'][1]['weight'] = 2; break;
            case 'stable_time': $data['answers'][1]['created_at'] = '2026-10-10T00:00:00Z'; break;
            case 'stable_eligibility_time': $data['answers'][1]['eligibility'][0]['created_at'] = '2026-10-10T00:00:00Z'; break;
            case 'stable_owner': $data['answers'][1]['eligibility'][0]['id'] = $data['answers'][0]['eligibility'][1]['id']; unset($data['answers'][0]['eligibility'][1]); break;
        }
        try {
            $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
            self::fail('Expected invalid catalog');
        } catch (\InvalidArgumentException $e) {
            self::assertSame($original['answers'][0]['text'], $db->fetchOne('SELECT text FROM answer WHERE id = ?', [$original['answers'][0]['id']]));
            self::assertSame(5, (int) $db->fetchOne('SELECT COUNT(*) FROM answer'));
        }
    }

    public static function invalidCatalogs(): iterable
    {
        foreach (['version','uuid','time','offset','empty','nul','locale','enabled','intent','topic','any_topic','mechanism','duplicate_pair','duplicate_answer','duplicate_eligibility','no_universal','unknown','stable_time','stable_eligibility_time','stable_owner'] as $case) {
            yield $case => [$case];
        }
    }

    public function testDatabaseFailureRollsBackWholeImport(): void
    {
        $db = BackendPersistenceTest::database();
        $importer = new CatalogImporter($db);
        $data = self::fixture();
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        $db->executeStatement("CREATE TRIGGER reject_catalog BEFORE UPDATE ON answer WHEN NEW.text = 'Reject' BEGIN SELECT RAISE(ABORT, 'synthetic failure'); END");
        $data['answers'][0]['text'] = 'Must roll back';
        $data['answers'][1]['text'] = 'Reject';
        try {
            $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
            self::fail('Expected DB error');
        } catch (\Doctrine\DBAL\Exception) {
            self::assertSame(self::fixture()['answers'][0]['text'], $db->fetchOne('SELECT text FROM answer ORDER BY id LIMIT 1'));
            self::assertFalse($db->isTransactionActive());
        }
    }

    public function testDuplicateTripleIsAlsoRejectedByDatabase(): void
    {
        $db = BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode(self::fixture(), JSON_THROW_ON_ERROR));
        $row = $db->fetchAssociative('SELECT * FROM answer_eligibility LIMIT 1');
        $row['id'] = '20000000-0000-4000-8000-000000000099';
        $row['humor_mechanism'] = 'hyperbole';
        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $db->insert('answer_eligibility', $row);
    }

    public function testDeferredCommitFailurePreservesTheWholeCatalog(): void
    {
        $db = BackendPersistenceTest::database();
        $data = self::fixture();
        $importer = new CatalogImporter($db);
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        $db->executeStatement('CREATE TABLE import_probe (answer_id TEXT REFERENCES answer(id) DEFERRABLE INITIALLY DEFERRED)');
        $db->executeStatement("CREATE TRIGGER fail_import_commit AFTER UPDATE ON answer BEGIN INSERT INTO import_probe VALUES ('missing-answer'); END");
        $data['answers'][0]['text'] = 'Must roll back';
        try {
            $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
            self::fail('Expected failed commit');
        } catch (\Doctrine\DBAL\Exception) {
            self::assertSame(self::fixture()['answers'][0]['text'], $db->fetchOne('SELECT text FROM answer ORDER BY id LIMIT 1'));
            self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM import_probe'));
            self::assertFalse($db->getNativeConnection()->inTransaction());
        }
    }
}
