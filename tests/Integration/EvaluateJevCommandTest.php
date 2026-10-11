<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Command\EvaluateJevCommand;
use App\Evaluation\CorpusLoader;
use App\Evaluation\EvaluationMetrics;
use App\Evaluation\EvaluationRunner;
use App\Infrastructure\Classification\JevResponseNormalizer;
use App\Infrastructure\Classification\JevRubric;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class EvaluateJevCommandTest extends TestCase
{
    public function testOfflineReplayRunsActualAdapterAndReportsKnownMetrics(): void
    {
        $tester = $this->tester();
        self::assertSame(Command::SUCCESS, $tester->execute([]));
        $report = json_decode($tester->getDisplay(), true, 32, JSON_THROW_ON_ERROR);
        self::assertSame('offline_fixture_replay', $report['mode']);
        self::assertSame('1.0.0', $report['corpus_version']);
        self::assertSame('1.0.0', $report['rubric_version']);
        self::assertSame('development', $report['split']);
        self::assertSame(54, $report['corpus_sample_count']);
        self::assertSame(6, $report['sample_count']);
        self::assertSame(4, $report['successful_count']);
        self::assertSame(3, $report['intent_accuracy']['numerator']);
        self::assertSame(3, $report['topic_accuracy']['numerator']);
        self::assertSame(2, $report['joint_accuracy']['numerator']);
        self::assertSame(6, $report['joint_accuracy']['denominator']);
        self::assertSame(2, $report['technical_failure_rate']['numerator']);
        self::assertSame(400, $report['token_usage']['known_input_tokens']);
        self::assertSame(80, $report['token_usage']['known_output_tokens']);
        self::assertSame(0, $report['retry_count']);
        self::assertSame(0.60, $report['http_policy']['timeout_seconds']);
        self::assertSame(0.80, $report['http_policy']['max_duration_seconds']);
        self::assertStringNotContainsString('do-not-print-secret', $tester->getDisplay());
        self::assertStringNotContainsString('Will my laptop', $tester->getDisplay());
    }

    public function testExplicitOfflineLimitBoundsAttemptedSamples(): void
    {
        $tester = $this->tester();
        self::assertSame(Command::SUCCESS, $tester->execute(['--limit' => '2']));
        $report = json_decode($tester->getDisplay(), true, 32, JSON_THROW_ON_ERROR);
        self::assertSame(2, $report['sample_count']);
        self::assertSame(2, $report['request_limit']);
    }

    public function testInvalidTimeoutPolicyFailsPreflight(): void
    {
        $tester = new CommandTester(new EvaluateJevCommand(new CorpusLoader(), new EvaluationRunner(new EvaluationMetrics()),
            new JevRubric(), new JevResponseNormalizer(), dirname(__DIR__, 2), 'jev-1.13.0', '', 0.8, 0.6));
        self::assertSame(Command::INVALID, $tester->execute([]));
        self::assertStringContainsString('max duration must be at least timeout', $tester->getDisplay());
    }

    #[DataProvider('preflightFailures')]
    public function testLiveAndInvalidOptionsFailBeforeNetwork(array $options, string $key): void
    {
        $tester = $this->tester($key);
        self::assertSame(Command::INVALID, $tester->execute($options));
        self::assertStringNotContainsString('do-not-print-secret', $tester->getDisplay());
    }

    public static function preflightFailures(): iterable
    {
        yield 'no explicit limit' => [['--live' => true], 'do-not-print-secret'];
        yield 'no configured key' => [['--live' => true, '--limit' => '3'], ''];
        yield 'invalid key newline' => [['--live' => true, '--limit' => '3'], "do-not-print-secret\n"];
        foreach (['0', '-1', '1.5', '1001', 'abc'] as $limit) {
            yield 'invalid limit '.$limit => [['--live' => true, '--limit' => $limit], 'do-not-print-secret'];
        }
        yield 'invalid split' => [['--split' => 'unknown'], ''];
        yield 'mixed modes' => [['--live' => true, '--limit' => '3', '--offline-fixtures' => 'some.json'], 'do-not-print-secret'];
        yield 'missing offline file' => [['--offline-fixtures' => '/nonexistent-fixture.json'], ''];
        yield 'holdout requires matching fixtures' => [['--split' => 'holdout'], ''];
    }

    private function tester(string $key = 'do-not-print-secret'): CommandTester
    {
        return new CommandTester(new EvaluateJevCommand(new CorpusLoader(), new EvaluationRunner(new EvaluationMetrics()),
            new JevRubric(), new JevResponseNormalizer(), dirname(__DIR__, 2), 'jev-1.13.0', $key));
    }
}
