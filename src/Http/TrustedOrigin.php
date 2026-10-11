<?php

declare(strict_types=1);
namespace App\Http;

final readonly class TrustedOrigin
{
    public function __construct(public string $origin)
    {
        if (!preg_match('~\Ahttps?://(?:[a-z0-9.-]+|\[[0-9a-f:]+\])(?::[0-9]{1,5})?\z~i', $origin)) {
            throw new \InvalidArgumentException('Configure an HTTP(S) application origin without a path.');
        }
        $port = parse_url($origin, PHP_URL_PORT);
        if ($port === false || ($port !== null && ($port < 1 || $port > 65535))) {
            throw new \InvalidArgumentException('Invalid application origin port.');
        }
    }

    public function url(string $path): string
    {
        return $this->origin.$path;
    }
}
