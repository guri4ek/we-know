<?php

declare(strict_types=1);
namespace App\Domain\Answer;

interface RandomIndexSource
{
    /** Return an index from 0 through count - 1, uniformly in production. */
    public function pick(int $count): int;
}
