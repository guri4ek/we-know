<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Answer\{AnswerSelector, Eligibility, RandomIndexSource, UtcTime, Uuid};
use App\Domain\Classification\{ClassificationFailure, ClassificationSuccess, FailureReason, Intent, Topic};
use App\Infrastructure\Persistence\{CatalogImporter, CatalogRepository};
use PHPUnit\Framework\TestCase;

final class EditorialCatalogTest extends TestCase
{
    private function catalog(): array
    {
        return json_decode(file_get_contents(dirname(__DIR__, 2).'/docs/content/seed-v2/catalog.json'), true, flags: JSON_THROW_ON_ERROR);
    }

    public function testCompleteExpandedCatalogIntegrityAndExactRepeatImport(): void
    {
        $catalog = $this->catalog();
        self::assertGreaterThan(24, count($catalog['answers']), 'The complete editorial catalog must include the expansion.');
        self::assertSame(2, $catalog['version']);
        $ids = $texts = $triples = $answers = $eligibilities = [];
        foreach ($catalog['answers'] as $answer) {
            self::assertTrue(Uuid::valid($answer['id']));
            self::assertTrue(UtcTime::valid($answer['created_at']));
            self::assertSame('en', $answer['locale']);
            self::assertTrue($answer['enabled']);
            self::assertArrayNotHasKey($answer['id'], $ids);
            $ids[$answer['id']] = true;
            $normalized = mb_strtolower(preg_replace('/\s+/u', ' ', trim($answer['text'])));
            self::assertArrayNotHasKey($normalized, $texts);
            $texts[$normalized] = true;
            foreach ($answer['eligibility'] as $row) {
                self::assertTrue(Uuid::valid($row['id']));
                self::assertTrue(UtcTime::valid($row['created_at']));
                self::assertArrayNotHasKey($row['id'], $ids);
                $ids[$row['id']] = true;
                self::assertTrue($row['intent'] === 'ANY' || Intent::tryFrom($row['intent']) !== null);
                self::assertNotNull(Topic::tryFrom($row['topic']));
                self::assertContains($row['humor_mechanism'], Eligibility::MECHANISMS);
                if ($row['intent'] === 'ANY') { self::assertSame('GENERAL', $row['topic']); }
                $triple = $answer['id'].'/'.$row['intent'].'/'.$row['topic'];
                self::assertArrayNotHasKey($triple, $triples);
                $triples[$triple] = true;
                $row['answer_id'] = $answer['id'];
                $eligibilities[$row['id']] = $row;
            }
            unset($answer['eligibility']);
            $answer['enabled'] = (int) $answer['enabled'];
            $answers[$answer['id']] = $answer;
        }
        ksort($answers); ksort($eligibilities);
        $db = BackendPersistenceTest::database();
        $importer = new CatalogImporter($db);
        $json = json_encode($catalog, JSON_THROW_ON_ERROR);
        foreach ([1, 2] as $import) {
            $importer->import($json);
            self::assertEquals(array_values($answers), $db->fetchAllAssociative('SELECT * FROM answer ORDER BY id'));
            self::assertEquals(array_values($eligibilities), $db->fetchAllAssociative('SELECT * FROM answer_eligibility ORDER BY id'));
        }
        $before = $db->fetchAllAssociative('SELECT * FROM answer ORDER BY id');
        $db->executeStatement("CREATE TRIGGER editorial_rollback BEFORE UPDATE ON answer WHEN NEW.text = 'Deliberate failure' BEGIN SELECT RAISE(ABORT, 'offline test'); END");
        $catalog['answers'][0]['text'] = 'Must roll back';
        $catalog['answers'][1]['text'] = 'Deliberate failure';
        try {
            $importer->import(json_encode($catalog, JSON_THROW_ON_ERROR));
            self::fail('Expected transactional failure');
        } catch (\Doctrine\DBAL\Exception) {
            self::assertSame($before, $db->fetchAllAssociative('SELECT * FROM answer ORDER BY id'));
        }
        self::assertSame('ok', $db->fetchOne('PRAGMA integrity_check'));
        self::assertSame([], $db->fetchAllAssociative('PRAGMA foreign_key_check'));
        self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
        $db->close();
    }

    public function testAllClassificationPairsUseFirstPoolAndEveryIndexIgnoresMechanism(): void
    {
        $db = BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode($this->catalog(), JSON_THROW_ON_ERROR));
        $pools = [];
        foreach ($this->catalog()['answers'] as $answer) {
            foreach ($answer['eligibility'] as $row) { $pools[$row['intent'].'/'.$row['topic']][] = $answer['id']; }
        }
        foreach ($pools as &$ids) { sort($ids); } unset($ids);
        $random = new class implements RandomIndexSource {
            public int $index = 0;
            public int $count = 0;
            public function pick(int $count): int { $this->count = $count; return $this->index; }
        };
        $selector = new AnswerSelector(new CatalogRepository($db), $random);
        foreach (Intent::cases() as $intent) {
            foreach (Topic::cases() as $topic) {
                $result = new ClassificationSuccess($intent, $topic, 'offline-double', 1, 1, [], [], 0, 0, 0);
                $exact = $intent->value.'/'.$topic->value;
                $general = $intent->value.'/GENERAL';
                $expected = isset($pools[$exact]) ? $exact : (isset($pools[$general]) ? $general : 'ANY/GENERAL');
                $tier = $expected === $exact ? 'exact' : ($expected === $general ? 'intent_general' : 'universal');
                foreach ($pools[$expected] as $index => $id) {
                    $random->index = $index;
                    $selection = $selector->select($result);
                    self::assertSame($id, $selection->candidate->answer->id);
                    self::assertSame($tier, $selection->context['tier']);
                    self::assertSame(count($pools[$expected]), $random->count);
                }
            }
        }
        // Changing every mechanism must leave every reachable index in every pool unchanged.
        $db->executeStatement("UPDATE answer_eligibility SET humor_mechanism = 'hyperbole'");
        foreach ($pools as $pool => $ids) {
            [$intent, $topic] = explode('/', $pool);
            $result = $intent === 'ANY' ? new ClassificationFailure(FailureReason::TIMEOUT, 0)
                : new ClassificationSuccess(Intent::from($intent), Topic::from($topic), 'offline-double', 1, 1, [], [], 0, 0, 0);
            foreach ($ids as $index => $id) {
                $random->index = $index;
                $selection = $selector->select($result);
                self::assertSame($id, $selection->candidate->answer->id);
                self::assertSame(count($ids), $random->count);
                if ($intent === 'ANY') {
                    self::assertSame('classification_failed', $selection->context['reason']);
                    self::assertSame('universal', $selection->context['tier']);
                }
            }
        }
        $db->close();
    }
}
