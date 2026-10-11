<?php

declare(strict_types=1);
namespace App\Application;

use App\Domain\Answer\{AnswerSelector, AnswerUnavailable, NoAvailableAnswer, Question, Uuid, UtcTime, Verdict};
use App\Domain\Classification\{ClassificationFailure, ClassificationSuccess, FailureReason, QuestionClassifier};
use App\Infrastructure\Persistence\VerdictRepository;
use App\Infrastructure\Persistence\Transaction;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class AnswerQuestion
{
    public function __construct(private readonly QuestionClassifier $classifier, private readonly AnswerSelector $selector, private readonly VerdictRepository $verdicts, private readonly Connection $db, private readonly string $classifierProvider, private readonly string $requestedModel) {}

    public function answer(Question $question): Verdict
    {
        // The external call completes before acquiring any database transaction/locks.
        $classification = $this->classifier->classify($question->text);
        $context = ['requested_model' => $this->requestedModel];
        if ($classification instanceof ClassificationFailure && $classification->reason === FailureReason::CONFIGURATION) {
            $context['skipped_reason'] = 'configuration';
        } else {
            $context = ['provider' => $this->classifierProvider] + $context;
        }
        if ($classification instanceof ClassificationSuccess) {
            $context += ['resolved_model' => $classification->model, 'intent' => $classification->intent->value, 'topic' => $classification->topic->value];
        } else {
            $context['failure_reason'] = $classification->reason->value;
        }
        try {
            return Transaction::run($this->db, function () use ($question, $classification, $context): Verdict {
                $selection = $this->selector->select($classification);
                $verdict = new Verdict(Uuid::v4(), $question->text, $selection->candidate->answer->text, $selection->candidate->eligibility->id, ['classifier' => $context, 'selection' => $selection->context], UtcTime::now());
                $this->verdicts->insert($verdict);

                return $verdict;
            });
        } catch (Exception|NoAvailableAnswer) {
            // Never chain a database exception: it may contain connection details.
            throw new AnswerUnavailable('An answer is temporarily unavailable.');
        }
    }
}
