<?php

declare(strict_types=1);

namespace App\Domain\Classification;

interface QuestionClassifier
{
    public function classify(string $question): ClassificationSuccess|ClassificationFailure;
}
