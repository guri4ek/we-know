<?php

declare(strict_types=1);
namespace App\Infrastructure\Persistence;

use App\Domain\Answer\Verdict;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

final class VerdictRepository
{
    public function __construct(private readonly Connection $db) {}

    public function insert(Verdict $verdict): void
    {
        $this->db->insert('verdict', ['id' => $verdict->id, 'question_snapshot' => $verdict->questionSnapshot, 'answer_snapshot' => $verdict->answerSnapshot, 'answer_eligibility_id' => $verdict->answerEligibilityId, 'decision_context' => $verdict->decisionContext, 'created_at' => $verdict->createdAt], ['decision_context' => Types::JSON]);
    }

    public function find(string $id): ?Verdict
    {
        $row = $this->db->fetchAssociative('SELECT * FROM verdict WHERE id = ?', [$id]);
        if ($row === false) {
            return null;
        }

        return new Verdict($row['id'], $row['question_snapshot'], $row['answer_snapshot'], $row['answer_eligibility_id'], json_decode($row['decision_context'], true, 64, JSON_THROW_ON_ERROR), $row['created_at']);
    }
}
