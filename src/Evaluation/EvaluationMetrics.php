<?php

declare(strict_types=1);

namespace App\Evaluation;

use App\Domain\Classification\ClassificationFailure;
use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\Intent;
use App\Domain\Classification\Topic;

final class EvaluationMetrics
{
    /** @param list<array{sample: array, result: ClassificationSuccess|ClassificationFailure}> $observations */
    public function summarize(array $observations): array
    {
        $count = count($observations);
        $successes = $intentCorrect = $topicCorrect = $jointCorrect = $inputTokens = $outputTokens = 0;
        $intentMatrix = $this->matrix(Intent::cases());
        $topicMatrix = $this->matrix(Topic::cases());
        $failures = $models = $latencies = $rows = [];
        foreach ($observations as ['sample' => $sample, 'result' => $result]) {
            $latencies[] = $result->latencyMs;
            $row = ['id' => $sample['id'], 'expected_intent' => $sample['intent'], 'expected_topic' => $sample['topic'], 'latency_ms' => $result->latencyMs];
            if ($result instanceof ClassificationFailure) {
                $failures[$result->reason->value] = ($failures[$result->reason->value] ?? 0) + 1;
                ++$intentMatrix[$sample['intent']]['TECHNICAL_FAILURE'];
                ++$topicMatrix[$sample['topic']]['TECHNICAL_FAILURE'];
                $row += ['failure' => $result->reason->value, 'http_status' => $result->httpStatus];
            } else {
                ++$successes;
                $intentMatch = $sample['intent'] === $result->intent->value;
                $topicMatch = $sample['topic'] === $result->topic->value;
                $intentCorrect += (int) $intentMatch;
                $topicCorrect += (int) $topicMatch;
                $jointCorrect += (int) ($intentMatch && $topicMatch);
                ++$intentMatrix[$sample['intent']][$result->intent->value];
                ++$topicMatrix[$sample['topic']][$result->topic->value];
                $inputTokens += $result->inputTokens;
                $outputTokens += $result->outputTokens;
                $models[$result->model] = ($models[$result->model] ?? 0) + 1;
                $row += ['intent' => $result->intent->value, 'topic' => $result->topic->value, 'model' => $result->model,
                    'intent_confidence' => $result->intentConfidence, 'topic_confidence' => $result->topicConfidence,
                    'input_tokens' => $result->inputTokens, 'output_tokens' => $result->outputTokens];
            }
            $rows[] = $row;
        }
        sort($latencies, SORT_NUMERIC);

        return [
            'sample_count' => $count,
            'successful_count' => $successes,
            'resolved_models' => $models,
            'intent_accuracy' => $this->ratio($intentCorrect, $count),
            'topic_accuracy' => $this->ratio($topicCorrect, $count),
            'joint_accuracy' => $this->ratio($jointCorrect, $count),
            'accuracy_among_successes' => [
                'intent' => $this->ratio($intentCorrect, $successes),
                'topic' => $this->ratio($topicCorrect, $successes),
                'joint' => $this->ratio($jointCorrect, $successes),
            ],
            'technical_failure_rate' => $this->ratio($count - $successes, $count),
            'technical_failures_by_reason' => $failures,
            'confusion_matrices' => ['orientation' => 'rows=expected; columns=predicted', 'intent' => $intentMatrix, 'topic' => $topicMatrix],
            'latency_ms' => ['sample_count' => $count, 'method' => 'nearest_rank; all attempts including failures',
                'p50' => $count ? $latencies[(int) ceil($count * 0.50) - 1] : null,
                'p95' => $count ? $latencies[(int) ceil($count * 0.95) - 1] : null],
            'token_usage' => ['known_input_tokens' => $inputTokens, 'known_output_tokens' => $outputTokens,
                'sample_count_with_usage' => $successes, 'sample_count_without_usage' => $count - $successes,
                'note' => 'Only validated successes have known usage; failed calls may still be billed.'],
            'samples' => $rows,
        ];
    }

    private function ratio(int $numerator, int $denominator): array
    {
        return ['numerator' => $numerator, 'denominator' => $denominator, 'value' => $denominator ? $numerator / $denominator : null];
    }

    private function matrix(array $categories): array
    {
        $labels = array_column($categories, 'value');
        $columns = array_fill_keys([...$labels, 'TECHNICAL_FAILURE'], 0);

        return array_fill_keys($labels, $columns);
    }
}
