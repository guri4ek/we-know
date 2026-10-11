<?php

declare(strict_types=1);

namespace App\Domain\Classification;

enum Intent: string
{
    case YES_NO = 'YES_NO';
    case WHEN = 'WHEN';
    case WHY = 'WHY';
    case HOW = 'HOW';
    case WHO = 'WHO';
    case WHAT = 'WHAT';
    case HOW_MANY = 'HOW_MANY';
    case CHOICE = 'CHOICE';
    case OTHER = 'OTHER';
}
