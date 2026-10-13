<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Classification\Intent;
use App\Domain\Classification\Topic;
use App\Evaluation\CorpusLoader;
use App\Infrastructure\Classification\JevRubric;
use PHPUnit\Framework\TestCase;

final class TaxonomyTest extends TestCase
{
    public function testExactV2CategoriesAndEligibilityExclusion(): void
    {
        self::assertSame(TaxonomyV2ContractTest::INTENTS, array_column(Intent::cases(), 'value'));
        self::assertSame(TaxonomyV2ContractTest::TOPICS, array_column(Topic::cases(), 'value'));
        self::assertNull(Intent::tryFrom('ANY'));
        self::assertNull(Topic::tryFrom('ANY'));
        self::assertNull(Intent::tryFrom('when'));
        $rubric = new JevRubric();
        self::assertSame(array_column(Intent::cases(), 'value'), array_keys($rubric->intentCriteria()));
        self::assertSame(array_column(Topic::cases(), 'value'), array_keys($rubric->topicCriteria()));
        foreach ([...$rubric->intentCriteria(), ...$rubric->topicCriteria()] as $definition) {
            self::assertNotSame('', trim($definition));
        }
    }

    public function testFrozenV2CorpusHasOneHundredOriginalQuestionsAndDisjointGroups(): void
    {
        $root = dirname(__DIR__, 2);
        $corpus = (new CorpusLoader())->load($root.'/docs/evaluation/corpus-v2/development.json', 'development');
        self::assertCount(100, $corpus['samples']);
        self::assertCount(100, array_unique(array_column($corpus['samples'], 'source_line')));
        self::assertSame(['ordinary'=>50,'indirect'=>20,'no_outcome'=>10,'boundary'=>20], array_count_values(array_column($corpus['samples'], 'group')));
        self::assertSame([], array_diff(array_column(Intent::cases(), 'value'), array_column($corpus['samples'], 'intent')));
        self::assertSame([], array_diff(array_column(Topic::cases(), 'value'), array_column($corpus['samples'], 'topic')));
        $questions = file($root.'/docs/research/questions-v1/questions.txt', FILE_IGNORE_NEW_LINES);
        foreach ($corpus['samples'] as $sample) {
            self::assertSame($questions[$sample['source_line'] - 1], $sample['question']);
            self::assertNotSame('', $sample['ambiguity_note']);
        }
    }

    public function testOtherTaxonomiesAreExplicitlyUnsupported(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CorpusLoader())->load(dirname(__DIR__, 2).'/docs/evaluation/corpus-v1/development.json', 'development');
    }
}
