<?php

declare(strict_types=1);
namespace App\Domain\Answer;

use App\Domain\Classification\{ClassificationFailure, ClassificationSuccess};

final class AnswerSelector
{
    public function __construct(private readonly AnswerCatalog $catalog, private readonly RandomIndexSource $random) {}

    public function select(ClassificationSuccess|ClassificationFailure $classification): Selection
    {
        $pools = [];
        if ($classification instanceof ClassificationSuccess) {
            $pools[] = [$classification->intent->value, $classification->topic->value, 'exact', null];
            if ($classification->topic->value !== 'GENERAL') {
                $pools[] = [$classification->intent->value, 'GENERAL', 'intent_general', 'exact_pool_empty'];
            }
        }
        $pools[] = ['ANY', 'GENERAL', 'universal', $classification instanceof ClassificationFailure ? 'classification_failed' : 'intent_pools_empty'];
        foreach ($pools as [$intent, $topic, $tier, $reason]) {
            $unique = [];
            foreach ($this->catalog->pool($intent, $topic) as $candidate) {
                $unique[$candidate->answer->id] ??= $candidate;
            }
            $candidates = array_values($unique);
            if ($candidates === []) {
                continue;
            }
            $count = count($candidates);
            $index = $this->random->pick($count);
            if ($index < 0 || $index >= $count) {
                throw new \LogicException('Random source returned an invalid index.');
            }
            $candidate = $candidates[$index];
            $context = ['tier' => $tier, 'pool' => ['intent' => $intent, 'topic' => $topic], 'candidate_count' => $count, 'algorithm' => 'uniform_random', 'humor_mechanism' => $candidate->eligibility->humorMechanism];
            if ($reason !== null) {
                $context['reason'] = $reason;
            }

            return new Selection($candidate, $context);
        }
        throw new NoAvailableAnswer('No available answer.');
    }
}
