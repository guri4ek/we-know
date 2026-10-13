<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Classification\ClassificationFailure;
use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\FailureReason;
use App\Domain\Classification\QuestionClassifier;
use App\Evaluation\EvaluationMetrics;
use App\Evaluation\EvaluationRunner;
use PHPUnit\Framework\TestCase;

final class EvaluationRunnerTest extends TestCase
{
    public function testRunsOneCallPerSelectedSampleAndRetainsVersionEvidence(): void
    {
        $classifier = new class implements QuestionClassifier {
            public array $questions = [];

            public function classify(string $question): ClassificationSuccess|ClassificationFailure
            {
                $this->questions[] = $question;

                return new ClassificationFailure(FailureReason::TIMEOUT, 10.0);
            }
        };
        $corpus = ['taxonomy_version' => '2.0.0', 'rubric_version' => '2.0.0', 'version' => 'fixture-v2', 'split' => 'development', 'label_status' => 'author_labels_not_independently_reviewed',
            'total_sample_count' => 54, 'samples' => [
                ['id' => 'a', 'question' => 'When?', 'intent' => 'WHEN', 'topic' => 'GENERAL'],
                ['id' => 'b', 'question' => 'Why?', 'intent' => 'EXPLAIN', 'topic' => 'GENERAL'],
            ]];
        $report = (new EvaluationRunner(new EvaluationMetrics()))->run($corpus, $classifier, 'requested-model', 'live');
        self::assertSame(['When?', 'Why?'], $classifier->questions);
        self::assertSame(2, $report['sample_count']);
        self::assertSame(54, $report['corpus_sample_count']);
        self::assertSame('requested-model', $report['requested_model']);
        self::assertSame('fixture-v2', $report['corpus_version']);
        self::assertSame('2.0.0', $report['rubric_version']);
        self::assertSame(2, $report['technical_failure_rate']['numerator']);
    }
    public function testFrozenHundredQuestionRunCallsEachOriginalExactlyOnce(): void
    {
        $corpus = (new \App\Evaluation\CorpusLoader())->load(dirname(__DIR__, 2).'/docs/evaluation/corpus-v2/development.json', 'development');
        $classifier = new class implements QuestionClassifier {
            public array $questions = [];
            public function classify(string $question): ClassificationFailure
            {
                $this->questions[] = $question;
                return new ClassificationFailure(FailureReason::TIMEOUT, 0);
            }
        };
        $report = (new EvaluationRunner(new EvaluationMetrics()))->run($corpus, $classifier, 'offline-double', 'offline_double');
        self::assertSame(array_column($corpus['samples'], 'question'), $classifier->questions);
        self::assertSame(100, $report['sample_count']);
        self::assertSame('2.0.0', $report['taxonomy_version']);
    }

    public function testIncompatibleVersionsFailBeforeClassification(): void
    {
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->expects(self::never())->method('classify');
        $this->expectException(\InvalidArgumentException::class);
        (new EvaluationRunner(new EvaluationMetrics()))->run(['taxonomy_version'=>'1.0.0','rubric_version'=>'1.0.0','samples'=>[]], $classifier, 'offline', 'live');
    }
}
