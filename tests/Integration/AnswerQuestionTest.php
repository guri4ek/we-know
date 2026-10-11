<?php

declare(strict_types=1);
namespace App\Tests\Integration;

use App\Application\AnswerQuestion;
use App\Domain\Answer\{AnswerSelector, AnswerUnavailable, Question, RandomIndexSource, Uuid, UtcTime};
use App\Domain\Classification\{ClassificationFailure, ClassificationSuccess, FailureReason, QuestionClassifier};
use App\Infrastructure\Persistence\{CatalogImporter, CatalogRepository, VerdictRepository};
use App\Tests\Unit\AnswerSelectorTest;
use PHPUnit\Framework\TestCase;

final class AnswerQuestionTest extends TestCase
{
    public function testClassifiesBeforeTransactionAndPreservesSnapshotsAfterEditDisableDelete(): void
    {
        $db = BackendPersistenceTest::database();
        $importer = new CatalogImporter($db);
        $data = CatalogImportTest::fixture();
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->expects(self::once())->method('classify')->with('When? 🦉')->willReturnCallback(function () use ($db) {
            self::assertFalse($db->isTransactionActive());
            return AnswerSelectorTest::success();
        });
        $random = $this->createMock(RandomIndexSource::class);
        $random->expects(self::once())->method('pick')->with(2)->willReturn(0);
        $repository = new VerdictRepository($db);
        $service = new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), $repository, $db, 'synthetic', 'requested-model');
        $result = $service->answer(Question::from('  When? 🦉  '));
        self::assertTrue(Uuid::publicId($result->id));
        self::assertTrue(UtcTime::valid($result->createdAt));
        self::assertFalse($db->isTransactionActive());
        self::assertSame(['provider'=>'synthetic','requested_model'=>'requested-model','resolved_model'=>'resolved-model','intent'=>'WHEN','topic'=>'TECH'], $result->decisionContext['classifier']);
        $data['answers'][0]['text'] = 'Edited';
        $data['answers'][0]['enabled'] = false;
        $data['answers'][0]['eligibility'][1]['humor_mechanism'] = 'hyperbole';
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        $saved = $repository->find($result->id);
        self::assertEquals($result, $saved);
        array_shift($data['answers']);
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        $saved = $repository->find($result->id);
        self::assertNull($saved->answerEligibilityId);
        self::assertSame($result->answerSnapshot, $saved->answerSnapshot);
        self::assertSame($result->questionSnapshot, $saved->questionSnapshot);
        self::assertSame($result->decisionContext, $saved->decisionContext);
    }

    public function testFailureContextDoesNotInventLabelsOrResolvedModel(): void
    {
        $db = BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode(CatalogImportTest::fixture(), JSON_THROW_ON_ERROR));
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->method('classify')->willReturn(new ClassificationFailure(FailureReason::TIMEOUT, 800));
        $random = $this->createMock(RandomIndexSource::class);
        $random->method('pick')->willReturn(0);
        $verdict = (new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), new VerdictRepository($db), $db, 'synthetic', 'requested-model'))->answer(Question::from('Hello'));
        self::assertSame(['provider'=>'synthetic','requested_model'=>'requested-model','failure_reason'=>'timeout'], $verdict->decisionContext['classifier']);
        self::assertSame('ANY', $verdict->decisionContext['selection']['pool']['intent']);
        self::assertNotNull($verdict->answerEligibilityId);
        self::assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
    }

    public function testInsertFailureRollsBackAndDoesNotExposeDatabaseError(): void
    {
        $db = BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode(CatalogImportTest::fixture(), JSON_THROW_ON_ERROR));
        $db->executeStatement("CREATE TRIGGER reject_verdict BEFORE INSERT ON verdict BEGIN SELECT RAISE(ABORT, 'secret synthetic DSN'); END");
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->method('classify')->willReturn(AnswerSelectorTest::success());
        $random = $this->createMock(RandomIndexSource::class);
        $random->method('pick')->willReturn(0);
        try {
            (new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), new VerdictRepository($db), $db, 'synthetic', 'requested-model'))->answer(Question::from('When?'));
            self::fail('Expected failure');
        } catch (AnswerUnavailable $e) {
            self::assertNull($e->getPrevious());
            self::assertStringNotContainsString('secret', $e->getMessage());
            self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
            self::assertFalse($db->isTransactionActive());
        }
    }

    public function testRealDeferredForeignKeyFailureAtCommitRollsBackVerdict(): void
    {
        $db = BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode(CatalogImportTest::fixture(), JSON_THROW_ON_ERROR));
        $db->executeStatement('CREATE TABLE commit_probe (id TEXT PRIMARY KEY, answer_id TEXT REFERENCES answer(id) DEFERRABLE INITIALLY DEFERRED)');
        $db->executeStatement("CREATE TRIGGER fail_commit AFTER INSERT ON verdict BEGIN INSERT INTO commit_probe VALUES (NEW.id, 'missing-answer'); END");
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->method('classify')->willReturn(AnswerSelectorTest::success());
        $random = $this->createMock(RandomIndexSource::class);
        $random->method('pick')->willReturn(0);
        try {
            (new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), new VerdictRepository($db), $db, 'synthetic', 'requested-model'))->answer(Question::from('When?'));
            self::fail('Expected failed commit');
        } catch (AnswerUnavailable $e) {
            self::assertNull($e->getPrevious());
            self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
            self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM commit_probe'));
            self::assertFalse($db->isTransactionActive());
        }
    }

    public function testSkippedConfigurationFailureHasNoInventedProvider(): void
    {
        $db = BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode(CatalogImportTest::fixture(), JSON_THROW_ON_ERROR));
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->method('classify')->willReturn(new ClassificationFailure(FailureReason::CONFIGURATION, 0));
        $random = $this->createMock(RandomIndexSource::class);
        $random->method('pick')->willReturn(0);
        $result = (new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), new VerdictRepository($db), $db, 'typesafe', 'requested-model'))->answer(Question::from('When?'));
        self::assertSame(['requested_model'=>'requested-model', 'skipped_reason'=>'configuration', 'failure_reason'=>'configuration_error'], $result->decisionContext['classifier']);
    }

    public function testClassifierLabelsRemainDistinctFromSelectedFallbackMetadata(): void
    {
        $db = BackendPersistenceTest::database();
        $data = CatalogImportTest::fixture();
        $data['answers'][2]['eligibility'][0]['humor_mechanism'] = 'hyperbole';
        (new CatalogImporter($db))->import(json_encode($data, JSON_THROW_ON_ERROR));
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->method('classify')->willReturn(AnswerSelectorTest::success(\App\Domain\Classification\Intent::WHEN, \App\Domain\Classification\Topic::HEALTH));
        $random = $this->createMock(RandomIndexSource::class);
        $random->method('pick')->willReturn(0);
        $verdict = (new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), new VerdictRepository($db), $db, 'synthetic', 'requested-model'))->answer(Question::from('Synthetic health question'));
        self::assertSame('HEALTH', $verdict->decisionContext['classifier']['topic']);
        self::assertSame(['tier'=>'intent_general', 'pool'=>['intent'=>'WHEN','topic'=>'GENERAL'], 'candidate_count'=>1, 'algorithm'=>'uniform_random', 'humor_mechanism'=>'hyperbole', 'reason'=>'exact_pool_empty'], $verdict->decisionContext['selection']);
        self::assertSame($data['answers'][2]['eligibility'][0]['id'], $verdict->answerEligibilityId);
    }
}
