<?php

declare(strict_types=1);
namespace App\Infrastructure\Random;

use App\Domain\Answer\RandomIndexSource;

final class SecureRandomIndex implements RandomIndexSource
{
    public function pick(int $count): int
    {
        return random_int(0, $count - 1);
    }
}
