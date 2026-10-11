<?php

declare(strict_types=1);
namespace App\Domain\Answer;

final readonly class Selection
{
    public function __construct(public Candidate $candidate, public array $context) {}
}
