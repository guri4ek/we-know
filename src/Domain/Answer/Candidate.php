<?php

declare(strict_types=1);
namespace App\Domain\Answer;

final readonly class Candidate
{
    public function __construct(public Answer $answer, public Eligibility $eligibility)
    {
    }
}
