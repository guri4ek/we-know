<?php

declare(strict_types=1);

namespace App\Evaluation;

use App\Domain\Classification\Intent;
use App\Domain\Classification\Topic;
use App\Infrastructure\Classification\JevRubric;

final class CorpusLoader
{
    public function load(string $path, string $split): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new \InvalidArgumentException('Corpus is not readable.');
        }
        try {
            $corpus = json_decode(file_get_contents($path), true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Corpus must be valid JSON.');
        }
        if (!is_array($corpus) || !is_string($corpus['version'] ?? null) || $corpus['version'] === ''
            || ($corpus['taxonomy_version'] ?? null) !== JevRubric::TAXONOMY_VERSION
            || ($corpus['split'] ?? null) !== $split || ($corpus['language'] ?? null) !== 'en'
            || ($corpus['label_status'] ?? null) !== 'author_labels_not_independently_reviewed'
            || !is_array($corpus['samples'] ?? null) || !array_is_list($corpus['samples']) || $corpus['samples'] === []
        ) {
            throw new \InvalidArgumentException('Invalid corpus metadata or samples.');
        }
        $ids = [];
        foreach ($corpus['samples'] as $sample) {
            if (!is_array($sample) || !is_string($sample['id'] ?? null) || $sample['id'] === ''
                || isset($ids[$sample['id']]) || !is_string($sample['question'] ?? null) || trim($sample['question']) === ''
                || !is_string($sample['intent'] ?? null) || Intent::tryFrom($sample['intent']) === null
                || !is_string($sample['topic'] ?? null) || Topic::tryFrom($sample['topic']) === null
            ) {
                throw new \InvalidArgumentException('Invalid corpus sample or duplicate ID.');
            }
            $ids[$sample['id']] = true;
        }

        return $corpus;
    }
}
