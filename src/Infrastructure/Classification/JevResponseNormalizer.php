<?php

declare(strict_types=1);

namespace App\Infrastructure\Classification;

use App\Domain\Classification\ClassificationFailure;
use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\FailureReason;
use App\Domain\Classification\Intent;
use App\Domain\Classification\Topic;

final class JevResponseNormalizer
{
    // Local schema policy, pending confirmation against a live response.
    public const PROBABILITY_SUM_TOLERANCE = 0.001;
    public const MAX_BODY_BYTES = 65536;

    public function normalize(string $body, float $latencyMs): ClassificationSuccess|ClassificationFailure
    {
        $invalid = new ClassificationFailure(FailureReason::INVALID_RESPONSE, $latencyMs, 200);
        if (strlen($body) > self::MAX_BODY_BYTES) {
            return $invalid;
        }
        try {
            $data = json_decode($body, false, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $invalid;
        }
        if (!$data instanceof \stdClass || !is_string($data->model ?? null) || trim($data->model) === ''
            || !$this->validChoice($data->answers->intent ?? null, Intent::cases())
            || !$this->validChoice($data->answers->topic ?? null, Topic::cases())
            || !is_int($data->usage->input_tokens ?? null) || $data->usage->input_tokens < 0
            || !is_int($data->usage->output_tokens ?? null) || $data->usage->output_tokens < 0
        ) {
            return $invalid;
        }

        $intent = $data->answers->intent;
        $topic = $data->answers->topic;

        return new ClassificationSuccess(
            Intent::from($intent->choice), Topic::from($topic->choice), $data->model,
            (float) $intent->confidence, (float) $topic->confidence,
            array_map(floatval(...), (array) $intent->probabilities),
            array_map(floatval(...), (array) $topic->probabilities),
            $data->usage->input_tokens, $data->usage->output_tokens, $latencyMs,
        );
    }

    /** @param list<Intent|Topic> $categories */
    private function validChoice(mixed $answer, array $categories): bool
    {
        if (!$answer instanceof \stdClass || ($answer->type ?? null) !== 'choice'
            || !is_string($answer->choice ?? null) || !$this->probability($answer->confidence ?? null)
            || !($answer->probabilities ?? null) instanceof \stdClass
        ) {
            return false;
        }
        $allowed = array_column($categories, 'value');
        $probabilities = (array) $answer->probabilities;
        if (!in_array($answer->choice, $allowed, true) || count($probabilities) !== count($allowed)
            || array_diff($allowed, array_keys($probabilities)) !== []
        ) {
            return false;
        }
        foreach ($probabilities as $value) {
            if (!$this->probability($value)) {
                return false;
            }
        }

        return abs(array_sum($probabilities) - 1.0) <= self::PROBABILITY_SUM_TOLERANCE
            && $probabilities[$answer->choice] + 1e-9 >= max($probabilities);
    }

    private function probability(mixed $value): bool
    {
        return (is_int($value) || is_float($value)) && is_finite((float) $value) && $value >= 0 && $value <= 1;
    }
}
