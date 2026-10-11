<?php

declare(strict_types=1);
namespace App\Domain\Answer;

final readonly class Question
{
    private function __construct(public string $text) {}

    public static function from(string $text): self
    {
        if (preg_match('//u', $text) !== 1 || str_contains($text, "\0")) {
            throw new \InvalidArgumentException('Invalid question.');
        }
        $text = preg_replace('/\A\s+|\s+\z/u', '', $text);
        $count = preg_match_all('/./us', $text);
        if ($count < 1 || $count > 500) {
            throw new \InvalidArgumentException('Invalid question.');
        }

        return new self($text);
    }
}
