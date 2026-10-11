<?php

declare(strict_types=1);
namespace App\Infrastructure\Persistence;

use App\Domain\Answer\{Answer, AnswerCatalog, Candidate, Eligibility};
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Types;

final class CatalogRepository implements AnswerCatalog
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function pool(string $intent, string $topic): array
    {
        // A PostgreSQL share lock holds the chosen rows stable through snapshot commit.
        $lock = $this->db->isTransactionActive() && $this->db->getDatabasePlatform() instanceof PostgreSQLPlatform ? ' FOR SHARE OF a, e' : '';
        $rows = $this->db->fetchAllAssociative('SELECT a.id, a.text, a.locale, a.enabled, a.created_at, e.id AS eligibility_id, e.intent, e.topic, e.humor_mechanism, e.created_at AS eligibility_created_at FROM answer a JOIN answer_eligibility e ON e.answer_id = a.id WHERE a.enabled = ? AND a.locale = ? AND e.intent = ? AND e.topic = ? ORDER BY a.id'.$lock, [true, 'en', $intent, $topic], [Types::BOOLEAN, Types::STRING, Types::STRING, Types::STRING]);

        return array_map(static fn (array $r): Candidate => new Candidate(
            new Answer($r['id'], $r['text'], $r['locale'], (bool) $r['enabled'], $r['created_at']),
            new Eligibility($r['eligibility_id'], $r['id'], $r['intent'], $r['topic'], $r['humor_mechanism'], $r['eligibility_created_at']),
        ), $rows);
    }
}
