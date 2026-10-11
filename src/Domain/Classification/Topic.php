<?php

declare(strict_types=1);

namespace App\Domain\Classification;

enum Topic: string
{
    case TECH = 'TECH';
    case WORK = 'WORK';
    case MONEY = 'MONEY';
    case LOVE = 'LOVE';
    case HEALTH = 'HEALTH';
    case LIFE = 'LIFE';
    case GENERAL = 'GENERAL';
}
