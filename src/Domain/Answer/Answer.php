<?php

declare(strict_types=1);
namespace App\Domain\Answer;

final readonly class Answer
{
    public function __construct(public string $id, public string $text, public string $locale, public bool $enabled, public string $createdAt)
    {
    }
}
