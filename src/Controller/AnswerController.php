<?php

declare(strict_types=1);
namespace App\Controller;

use App\Application\AnswerQuestion;
use App\Domain\Answer\{AnswerUnavailable, Question};
use App\Http\{ApiError, TrustedOrigin};
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AnswerController
{
    public function __construct(private readonly AnswerQuestion $answerQuestion, private readonly TrustedOrigin $origin, private readonly RateLimiterFactoryInterface $limiter) {}

    #[Route('/answer', name: 'app_answer', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        if ($request->headers->get('Origin') !== $this->origin->origin || $request->getSchemeAndHttpHost() !== $this->origin->origin || ($request->headers->has('Sec-Fetch-Site') && $request->headers->get('Sec-Fetch-Site') !== 'same-origin')) {
            return ApiError::response(403, 'forbidden_origin', 'Submit from this application origin.');
        }
        $contentType = strtolower($request->headers->get('Content-Type', ''));
        if (trim(explode(';', $contentType)[0]) !== 'application/json' || (preg_match('/charset\s*=\s*"?([^;"\s]+)/', $contentType, $charset) && $charset[1] !== 'utf-8')) {
            return ApiError::response(415, 'unsupported_media_type', 'Use application/json with UTF-8.');
        }
        if ((int) $request->headers->get('Content-Length', '0') > 8192) {
            return ApiError::response(413, 'body_too_large', 'Request body must not exceed 8 KiB.');
        }
        $stream = $request->getContent(true);
        $body = stream_get_contents($stream, 8193);
        if ($body === false || strlen($body) > 8192) {
            return ApiError::response(413, 'body_too_large', 'Request body must not exceed 8 KiB.');
        }
        try {
            $data = json_decode($body, false, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiError::response(400, 'invalid_json', 'Send a valid JSON object.');
        }
        try {
            if (!$data instanceof \stdClass || array_keys(get_object_vars($data)) !== ['question'] || !is_string($data->question)) {
                throw new \InvalidArgumentException();
            }
            $question = Question::from($data->question);
        } catch (\InvalidArgumentException) {
            return ApiError::response(422, 'invalid_question', 'Enter a question between 1 and 500 characters.');
        }
        $limit = $this->limiter->create($request->getClientIp() ?? 'unknown')->consume();
        if (!$limit->isAccepted()) {
            $retryAfter = max(1, $limit->getRetryAfter()->getTimestamp() - time());
            return ApiError::response(429, 'rate_limited', 'Please try again later.', ['Retry-After' => (string) $retryAfter]);
        }
        try {
            $verdict = $this->answerQuestion->answer($question);
        } catch (AnswerUnavailable) {
            return ApiError::response(503, 'answer_unavailable', 'An answer is temporarily unavailable.');
        }
        $url = '/a/'.$verdict->id;

        return new JsonResponse(['id' => $verdict->id, 'question' => $verdict->questionSnapshot, 'answer' => $verdict->answerSnapshot, 'url' => $url], 201, ['Location' => $url, 'Cache-Control' => 'no-store']);
    }
}
