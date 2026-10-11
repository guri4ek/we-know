<?php

declare(strict_types=1);
namespace App\Domain\Answer;

interface AnswerCatalog
{
    /** @return list<Candidate> */
    public function pool(string $intent, string $topic): array;
}
