<?php

declare(strict_types=1);
namespace App\Domain\Answer;

final readonly class Verdict
{
    public function __construct(public string $id, public string $questionSnapshot, public string $answerSnapshot, public ?string $answerEligibilityId, public array $decisionContext, public string $createdAt) {}
}
