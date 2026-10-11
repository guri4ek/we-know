<?php

declare(strict_types=1);

namespace App\Domain\Classification;

enum FailureReason: string
{
    case CONFIGURATION = 'configuration_error';
    case INVALID_RESPONSE = 'invalid_response';
    case AUTH = 'auth_error';
    case INVALID_REQUEST = 'invalid_request';
    case RATE_LIMIT = 'rate_limited';
    case OVERLOADED = 'overloaded';
    case PROVIDER = 'provider_error';
    case HTTP = 'http_error';
    case TIMEOUT = 'timeout';
    case TRANSPORT = 'transport_error';
}
