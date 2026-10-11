<?php

declare(strict_types=1);

namespace App\Domain\Classification;

final readonly class ClassificationSuccess
{
    /** @param array<string, float> $intentProbabilities @param array<string, float> $topicProbabilities */
    public function __construct(
        public Intent $intent,
        public Topic $topic,
        public string $model,
        public float $intentConfidence,
        public float $topicConfidence,
        public array $intentProbabilities,
        public array $topicProbabilities,
        public int $inputTokens,
        public int $outputTokens,
        public float $latencyMs,
    ) {
    }
}
