<?php

declare(strict_types=1);

namespace App\Domain\Classification;

final readonly class ClassificationFailure
{
    public function __construct(
        public FailureReason $reason,
        public float $latencyMs,
        public ?int $httpStatus = null,
    ) {
    }
}
