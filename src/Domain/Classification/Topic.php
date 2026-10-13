<?php

declare(strict_types=1);

namespace App\Domain\Classification;

enum Topic: string
{
    case TECH = 'TECH';
    case WORK_STUDY = 'WORK_STUDY';
    case MONEY = 'MONEY';
    case RELATIONSHIPS = 'RELATIONSHIPS';
    case HEALTH = 'HEALTH';
    case FOOD = 'FOOD';
    case HOME = 'HOME';
    case NATURE = 'NATURE';
    case TRAVEL = 'TRAVEL';
    case SPORTS = 'SPORTS';
    case ENTERTAINMENT = 'ENTERTAINMENT';
    case LANGUAGE = 'LANGUAGE';
    case GENERAL = 'GENERAL';
}
