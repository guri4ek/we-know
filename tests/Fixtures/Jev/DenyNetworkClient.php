<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Jev;

use Symfony\Component\HttpClient\MockHttpClient;

final class DenyNetworkClient
{
    public static function create(): MockHttpClient
    {
        return new MockHttpClient(static function (): never {
            throw new \LogicException('Unexpected outbound request in default test suite. Inject a response fixture.');
        });
    }
}
