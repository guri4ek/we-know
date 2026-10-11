<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Classification\ClassificationFailure;
use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\FailureReason;
use App\Domain\Classification\Intent;
use App\Domain\Classification\Topic;
use App\Evaluation\EvaluationMetrics;
use PHPUnit\Framework\TestCase;

final class EvaluationMetricsTest extends TestCase
{
    public function testDeterministicMetricsIncludeFailuresInDenominators(): void
    {
        $sample = ['id' => 'fixture', 'intent' => 'WHEN', 'topic' => 'TECH'];
        $success = static fn (Intent $intent, Topic $topic, float $latency): ClassificationSuccess =>
            new ClassificationSuccess($intent, $topic, 'fixture-model', 1.0, 1.0, [], [], 100, 20, $latency);
        $report = (new EvaluationMetrics())->summarize([
            ['sample' => $sample, 'result' => $success(Intent::WHEN, Topic::TECH, 10)],
            ['sample' => $sample, 'result' => $success(Intent::WHEN, Topic::WORK, 50)],
            ['sample' => $sample, 'result' => $success(Intent::WHY, Topic::TECH, 20)],
            ['sample' => $sample, 'result' => $success(Intent::OTHER, Topic::GENERAL, 30)],
            ['sample' => $sample, 'result' => new ClassificationFailure(FailureReason::TIMEOUT, 40)],
        ]);
        self::assertSame(5, $report['sample_count']);
        self::assertSame(4, $report['successful_count']);
        self::assertSame(['numerator' => 2, 'denominator' => 5, 'value' => 0.4], $report['intent_accuracy']);
        self::assertSame(['numerator' => 2, 'denominator' => 5, 'value' => 0.4], $report['topic_accuracy']);
        self::assertSame(['numerator' => 1, 'denominator' => 5, 'value' => 0.2], $report['joint_accuracy']);
        self::assertSame(0.25, $report['accuracy_among_successes']['joint']['value']);
        self::assertSame(['numerator' => 1, 'denominator' => 5, 'value' => 0.2], $report['technical_failure_rate']);
        self::assertSame(['timeout' => 1], $report['technical_failures_by_reason']);
        self::assertSame(30.0, $report['latency_ms']['p50']);
        self::assertSame(50.0, $report['latency_ms']['p95']);
        self::assertSame(400, $report['token_usage']['known_input_tokens']);
        self::assertSame(80, $report['token_usage']['known_output_tokens']);
        self::assertSame(4, $report['token_usage']['sample_count_with_usage']);
        self::assertSame(1, $report['token_usage']['sample_count_without_usage']);
        self::assertSame(2, $report['confusion_matrices']['intent']['WHEN']['WHEN']);
        self::assertSame(1, $report['confusion_matrices']['intent']['WHEN']['WHY']);
        self::assertSame(1, $report['confusion_matrices']['intent']['WHEN']['TECHNICAL_FAILURE']);
        self::assertSame(2, $report['confusion_matrices']['topic']['TECH']['TECH']);
        self::assertSame(5, array_sum($report['confusion_matrices']['topic']['TECH']));
        self::assertSame(['fixture-model' => 4], $report['resolved_models']);
    }

    public function testEmptyAndAllFailedMetricsHaveNoFabricatedUsageOrAccuracy(): void
    {
        $metrics = new EvaluationMetrics();
        $empty = $metrics->summarize([]);
        self::assertNull($empty['intent_accuracy']['value']);
        self::assertNull($empty['technical_failure_rate']['value']);
        self::assertNull($empty['latency_ms']['p95']);
        $failed = $metrics->summarize([['sample' => ['id' => 'one', 'intent' => 'OTHER', 'topic' => 'GENERAL'],
            'result' => new ClassificationFailure(FailureReason::AUTH, 7)]]);
        self::assertSame(0, $failed['intent_accuracy']['value']);
        self::assertNull($failed['accuracy_among_successes']['joint']['value']);
        self::assertSame(1, $failed['technical_failure_rate']['value']);
        self::assertSame(7.0, $failed['latency_ms']['p50']);
        self::assertSame(1, $failed['token_usage']['sample_count_without_usage']);
    }
}
