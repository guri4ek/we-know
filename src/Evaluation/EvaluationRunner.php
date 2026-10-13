<?php

declare(strict_types=1);

namespace App\Evaluation;

use App\Domain\Classification\QuestionClassifier;
use App\Infrastructure\Classification\JevRubric;

final class EvaluationRunner
{
    public function __construct(private readonly EvaluationMetrics $metrics)
    {
    }

    public function run(array $corpus, QuestionClassifier $classifier, string $requestedModel, string $mode): array
    {
        if (($corpus['taxonomy_version'] ?? null) !== JevRubric::TAXONOMY_VERSION
            || ($corpus['rubric_version'] ?? null) !== JevRubric::VERSION
        ) {
            throw new \InvalidArgumentException('Unsupported corpus taxonomy/rubric combination.');
        }
        $observations = [];
        foreach ($corpus['samples'] as $sample) {
            $observations[] = ['sample' => $sample, 'result' => $classifier->classify($sample['question'])];
        }

        return [
            'report_version' => '1.0.0',
            'mode' => $mode,
            'recorded_at_utc' => gmdate('c'),
            'requested_model' => $requestedModel,
            'taxonomy_version' => JevRubric::TAXONOMY_VERSION,
            'rubric_version' => JevRubric::VERSION,
            'corpus_version' => $corpus['version'],
            'split' => $corpus['split'],
            'label_status' => $corpus['label_status'],
            'corpus_sample_count' => $corpus['total_sample_count'] ?? count($corpus['samples']),
            'latency_scope' => $mode === 'live' ? 'classifier wall time, including HTTP and parsing' : 'mock transport wall time; not provider performance',
        ] + $this->metrics->summarize($observations);
    }
}
