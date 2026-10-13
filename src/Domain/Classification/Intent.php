<?php

declare(strict_types=1);

namespace App\Domain\Classification;

enum Intent: string
{
    case YES_NO = 'YES_NO';
    case WHEN = 'WHEN';
    case DURATION = 'DURATION';
    case EXPLAIN = 'EXPLAIN';
    case METHOD = 'METHOD';
    case WHO = 'WHO';
    case IDENTIFY = 'IDENTIFY';
    case DESCRIBE = 'DESCRIBE';
    case WHERE = 'WHERE';
    case QUANTITY = 'QUANTITY';
    case CHOICE = 'CHOICE';
    case RECOMMEND = 'RECOMMEND';
    case COMPARE = 'COMPARE';
    case OTHER = 'OTHER';
}
