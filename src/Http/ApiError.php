<?php

declare(strict_types=1);
namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiError
{
    public static function response(int $status, string $code, string $message, array $headers = []): JsonResponse
    {
        return new JsonResponse(['error' => ['code' => $code, 'message' => $message]], $status, $headers + ['Cache-Control' => 'no-store']);
    }
}
