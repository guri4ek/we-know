<?php

declare(strict_types=1);
namespace App\Domain\Answer;

final readonly class Eligibility
{
    public const MECHANISMS = ['metaphor', 'hyperbole', 'oxymoron', 'paradox', 'false_causality', 'false_authority', 'reversal', 'bureaucratic_absurdity'];

    public function __construct(public string $id, public string $answerId, public string $intent, public string $topic, public string $humorMechanism, public string $createdAt)
    {
    }
}
