<?php

declare(strict_types=1);
namespace App\Infrastructure\Persistence;

use App\Domain\Answer\{Eligibility, Uuid, UtcTime};
use App\Domain\Classification\{Intent, Topic};
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

final class CatalogImporter
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function import(string $json): void
    {
        try {
            $data = json_decode($json, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Invalid catalog JSON.');
        }
        $this->keys($data, ['version', 'answers']);
        $this->require($data['version'] === 2 && is_array($data['answers']) && array_is_list($data['answers']), 'Unsupported catalog format.');
        $answers = $eligibilities = $triples = [];
        $universal = false;
        foreach ($data['answers'] as $answer) {
            $this->keys($answer, ['id', 'text', 'locale', 'enabled', 'created_at', 'eligibility']);
            $this->identity($answer);
            $this->require(!isset($answers[$answer['id']]), 'Duplicate answer ID.');
            $this->require(is_string($answer['text']) && preg_match('//u', $answer['text']) === 1 && preg_match('/\S/u', $answer['text']) === 1 && !str_contains($answer['text'], "\0"), 'Invalid answer text.');
            $this->require($answer['locale'] === 'en' && is_bool($answer['enabled']), 'Invalid locale or enabled flag.');
            $this->require(is_array($answer['eligibility']) && array_is_list($answer['eligibility']) && $answer['eligibility'] !== [], 'Explicit eligibility pairs required.');
            foreach ($answer['eligibility'] as $row) {
                $this->keys($row, ['id', 'intent', 'topic', 'humor_mechanism', 'created_at']);
                $this->identity($row);
                $this->require(!isset($eligibilities[$row['id']]), 'Duplicate eligibility ID.');
                $this->require(is_string($row['intent']) && ($row['intent'] === 'ANY' || Intent::tryFrom($row['intent']) !== null), 'Invalid intent.');
                $this->require(is_string($row['topic']) && Topic::tryFrom($row['topic']) !== null && ($row['intent'] !== 'ANY' || $row['topic'] === 'GENERAL'), 'Invalid topic/pair.');
                $this->require(in_array($row['humor_mechanism'], Eligibility::MECHANISMS, true), 'Invalid mechanism.');
                $triple = $answer['id'].'/'.$row['intent'].'/'.$row['topic'];
                $this->require(!isset($triples[$triple]), 'Duplicate eligibility triple.');
                $triples[$triple] = true;
                $row['answer_id'] = $answer['id'];
                $eligibilities[$row['id']] = $row;
                $universal = $universal || ($answer['enabled'] && $row['intent'] === 'ANY' && $row['topic'] === 'GENERAL');
            }
            unset($answer['eligibility']);
            $answers[$answer['id']] = $answer;
        }
        $this->require($universal, 'An enabled universal answer is required.');
        Transaction::run($this->db, function () use ($answers, $eligibilities): void {
            // Validate historical identities before removing or changing any catalog row.
            foreach (['answer' => $answers, 'answer_eligibility' => $eligibilities] as $table => $incoming) {
                foreach ($this->db->fetchAllAssociative('SELECT * FROM '.$table) as $old) {
                    if (isset($incoming[$old['id']])) {
                        $new = $incoming[$old['id']];
                        $this->require($old['created_at'] === $new['created_at'], 'Creation time is immutable.');
                        if ($table === 'answer_eligibility') {
                            $this->require($old['answer_id'] === $new['answer_id'], 'Eligibility ownership is immutable.');
                        }
                    }
                }
            }
            // Remove obsolete pairs first, allowing an existing pair to receive a new stable ID.
            foreach ($this->db->fetchFirstColumn('SELECT id FROM answer_eligibility') as $id) {
                if (!isset($eligibilities[$id])) {
                    $this->db->delete('answer_eligibility', ['id' => $id]);
                }
            }
            foreach ($this->db->fetchFirstColumn('SELECT id FROM answer') as $id) {
                if (!isset($answers[$id])) {
                    $this->db->delete('answer', ['id' => $id]);
                }
            }
            foreach ($answers as $row) {
                $this->save('answer', $row, ['enabled' => Types::BOOLEAN]);
            }
            foreach ($eligibilities as $row) {
                $this->save('answer_eligibility', $row);
            }
        });
    }

    private function save(string $table, array $row, array $types = []): void
    {
        if ($this->db->fetchOne('SELECT id FROM '.$table.' WHERE id = ?', [$row['id']]) !== false) {
            $id = $row['id'];
            unset($row['id'], $row['created_at']);
            $this->db->update($table, $row, ['id' => $id], $types);
        } else {
            $this->db->insert($table, $row, $types);
        }
    }

    private function identity(array $row): void
    {
        $this->require(is_string($row['id']) && Uuid::valid($row['id']), 'Invalid UUID.');
        $this->require(is_string($row['created_at']) && UtcTime::valid($row['created_at']), 'Invalid UTC creation time.');
    }

    private function keys(mixed $row, array $keys): void
    {
        $this->require(is_array($row) && count($row) === count($keys) && array_diff($keys, array_keys($row)) === [], 'Invalid catalog fields.');
    }

    private function require(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \InvalidArgumentException($message);
        }
    }
}
