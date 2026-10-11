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
    public function testExactV1CategoriesAndEligibilityExclusion(): void
    {
        self::assertSame(['YES_NO', 'WHEN', 'WHY', 'HOW', 'WHO', 'WHAT', 'HOW_MANY', 'CHOICE', 'OTHER'], array_column(Intent::cases(), 'value'));
        self::assertSame(['TECH', 'WORK', 'MONEY', 'LOVE', 'HEALTH', 'LIFE', 'GENERAL'], array_column(Topic::cases(), 'value'));
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

    public function testCorpusSplitsHaveDisjointIdsAndCoverAllCategories(): void
    {
        $loader = new CorpusLoader();
        $root = dirname(__DIR__, 2).'/docs/evaluation/corpus-v1/';
        $development = $loader->load($root.'development.json', 'development');
        $holdout = $loader->load($root.'holdout.json', 'holdout');
        self::assertSame(54, count($development['samples']));
        self::assertSame(27, count($holdout['samples']));
        self::assertSame([], array_intersect(array_column($development['samples'], 'id'), array_column($holdout['samples'], 'id')));
        self::assertSame([], array_intersect(array_column($development['samples'], 'question'), array_column($holdout['samples'], 'question')));
        foreach ([$development, $holdout] as $corpus) {
            self::assertSame([], array_diff(array_column(Intent::cases(), 'value'), array_column($corpus['samples'], 'intent')));
            self::assertSame([], array_diff(array_column(Topic::cases(), 'value'), array_column($corpus['samples'], 'topic')));
        }
        $tags = array_merge(...array_column($development['samples'], 'tags'));
        foreach (['duration', 'salary_career', 'technology_work', 'multiple_questions', 'negation', 'typo', 'non_question', 'injection_only', 'injection_question', 'explicit_alternatives'] as $tag) {
            self::assertContains($tag, $tags);
        }
    }
}
