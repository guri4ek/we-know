<?php

declare(strict_types=1);

namespace App\Infrastructure\Classification;

use App\Domain\Classification\ClassificationFailure;
use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\FailureReason;
use App\Domain\Classification\QuestionClassifier;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JevClassifier implements QuestionClassifier
{
    public const ENDPOINT = 'https://api.typesafe.ai/v1/systemone';
    public const DEFAULT_TIMEOUT = 0.60;
    public const DEFAULT_MAX_DURATION = 0.80;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly JevRubric $rubric,
        private readonly JevResponseNormalizer $normalizer,
        private readonly string $model,
        #[\SensitiveParameter] private readonly string $apiKey,
        private readonly float $timeout = self::DEFAULT_TIMEOUT,
        private readonly float $maxDuration = self::DEFAULT_MAX_DURATION,
    ) {
    }

    public function classify(string $question): ClassificationSuccess|ClassificationFailure
    {
        $start = hrtime(true);
        $elapsed = static fn (): float => (hrtime(true) - $start) / 1e6;
        if (trim($this->model) === '' || trim($this->apiKey) === '' || preg_match('/[\r\n]/', $this->apiKey)
            || !is_finite($this->timeout) || !is_finite($this->maxDuration)
            || $this->timeout <= 0 || $this->maxDuration < $this->timeout
        ) {
            return new ClassificationFailure(FailureReason::CONFIGURATION, $elapsed());
        }
        try {
            $response = $this->client->request('POST', self::ENDPOINT, [
                'auth_bearer' => $this->apiKey,
                'json' => $this->rubric->payload($question, $this->model),
                'timeout' => $this->timeout,
                'max_duration' => $this->maxDuration,
                'max_redirects' => 0,
            ]);
            $status = $response->getStatusCode();
            if ($status !== 200) {
                $response->cancel();
                $reason = match ($status) {
                    401, 403 => FailureReason::AUTH,
                    422 => FailureReason::INVALID_REQUEST,
                    429 => FailureReason::RATE_LIMIT,
                    529 => FailureReason::OVERLOADED,
                    default => $status >= 500 ? FailureReason::PROVIDER : FailureReason::HTTP,
                };

                return new ClassificationFailure($reason, $elapsed(), $status);
            }
            $body = $response->getContent(false);

            return $this->normalizer->normalize($body, $elapsed());
        } catch (TimeoutExceptionInterface) {
            return new ClassificationFailure(FailureReason::TIMEOUT, $elapsed());
        } catch (TransportExceptionInterface) {
            return new ClassificationFailure(FailureReason::TRANSPORT, $elapsed());
        }
    }
}
