<?php

declare(strict_types=1);
namespace App\Tests\Unit;

use App\Domain\Answer\{Answer, AnswerCatalog, AnswerSelector, Candidate, Eligibility, NoAvailableAnswer, RandomIndexSource};
use App\Domain\Classification\{ClassificationFailure, ClassificationSuccess, FailureReason, Intent, Topic};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AnswerSelectorTest extends TestCase
{
    public static function success(Intent $intent = Intent::WHEN, Topic $topic = Topic::TECH): ClassificationSuccess
    {
        return new ClassificationSuccess($intent, $topic, 'resolved-model', .9, .9, [], [], 10, 2, 1.0);
    }

    public static function candidate(string $id = 'a', string $mechanism = 'metaphor'): Candidate
    {
        return new Candidate(new Answer($id, 'Synthetic '.$id, 'en', true, '2026-10-11T00:00:00Z'), new Eligibility('e'.$id, $id, 'WHEN', 'TECH', $mechanism, '2026-10-11T00:00:00Z'));
    }

    #[DataProvider('tiers')]
    public function testFirstNonemptyTierAndGeneralNotRepeated(array $available, array $expectedCalls, string $tier, ?string $reason, string $intent, string $topic, bool $failure): void
    {
        $catalog = new class($available) implements AnswerCatalog {
            public array $calls = [];
            public function __construct(private array $available) {}
            public function pool(string $intent, string $topic): array {
                $this->calls[] = [$intent, $topic];
                return isset($this->available[$intent.'/'.$topic]) ? [AnswerSelectorTest::candidate()] : [];
            }
        };
        $random = $this->createMock(RandomIndexSource::class);
        $random->expects(self::once())->method('pick')->with(1)->willReturn(0);
        $classification = $failure ? new ClassificationFailure(FailureReason::TIMEOUT, 1) : self::success(Intent::from($intent), Topic::from($topic));
        $selected = (new AnswerSelector($catalog, $random))->select($classification);
        self::assertSame($expectedCalls, $catalog->calls);
        self::assertSame($tier, $selected->context['tier']);
        self::assertSame($reason, $selected->context['reason'] ?? null);
        self::assertSame($expectedCalls[array_key_last($expectedCalls)], [$selected->context['pool']['intent'], $selected->context['pool']['topic']]);
        self::assertSame('uniform_random', $selected->context['algorithm']);
    }

    public static function tiers(): iterable
    {
        yield 'exact' => [['WHEN/TECH'=>true,'WHEN/GENERAL'=>true,'ANY/GENERAL'=>true],[['WHEN','TECH']],'exact',null,'WHEN','TECH',false];
        yield 'intent' => [['WHEN/GENERAL'=>true,'ANY/GENERAL'=>true],[['WHEN','TECH'],['WHEN','GENERAL']],'intent_general','exact_pool_empty','WHEN','TECH',false];
        yield 'universal' => [['ANY/GENERAL'=>true],[['WHEN','TECH'],['WHEN','GENERAL'],['ANY','GENERAL']],'universal','intent_pools_empty','WHEN','TECH',false];
        yield 'general exact' => [['WHEN/GENERAL'=>true],[['WHEN','GENERAL']],'exact',null,'WHEN','GENERAL',false];
        yield 'no repeat general' => [['ANY/GENERAL'=>true],[['WHEN','GENERAL'],['ANY','GENERAL']],'universal','intent_pools_empty','WHEN','GENERAL',false];
        yield 'valid OTHER' => [['OTHER/GENERAL'=>true],[['OTHER','GENERAL']],'exact',null,'OTHER','GENERAL',false];
        yield 'failed direct universal' => [['OTHER/GENERAL'=>true,'ANY/GENERAL'=>true],[['ANY','GENERAL']],'universal','classification_failed','OTHER','GENERAL',true];
    }

    #[DataProvider('indices')]
    public function testUniqueCandidateCountAndFirstLastBoundary(int $index, string $id): void
    {
        $catalog = $this->createMock(AnswerCatalog::class);
        $catalog->method('pool')->willReturn([self::candidate('a'), self::candidate('a'), self::candidate('b', 'hyperbole')]);
        $random = $this->createMock(RandomIndexSource::class);
        $random->expects(self::once())->method('pick')->with(2)->willReturn($index);
        $selection = (new AnswerSelector($catalog, $random))->select(self::success());
        self::assertSame($id, $selection->candidate->answer->id);
        self::assertSame(2, $selection->context['candidate_count']);
        self::assertSame($selection->candidate->eligibility->humorMechanism, $selection->context['humor_mechanism']);
    }

    public static function indices(): iterable { yield [0,'a']; yield [1,'b']; }

    public function testEmptyCatalogDoesNotCallRandom(): void
    {
        $catalog = $this->createMock(AnswerCatalog::class);
        $catalog->method('pool')->willReturn([]);
        $random = $this->createMock(RandomIndexSource::class);
        $random->expects(self::never())->method('pick');
        $this->expectException(NoAvailableAnswer::class);
        (new AnswerSelector($catalog, $random))->select(new ClassificationFailure(FailureReason::TIMEOUT, 1));
    }

    public function testInvalidRandomIndexIsRejected(): void
    {
        $catalog = $this->createMock(AnswerCatalog::class);
        $catalog->method('pool')->willReturn([self::candidate()]);
        $random = $this->createMock(RandomIndexSource::class);
        $random->method('pick')->willReturn(1);
        $this->expectException(\LogicException::class);
        (new AnswerSelector($catalog, $random))->select(self::success());
    }
}
