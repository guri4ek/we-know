<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Classification\ClassificationFailure;
use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\FailureReason;
use App\Domain\Classification\Intent;
use App\Domain\Classification\Topic;
use App\Infrastructure\Classification\JevResponseNormalizer;
use App\Tests\Fixtures\Jev\ResponseFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class JevResponseNormalizerTest extends TestCase
{
    public function testSuccessIsTypedAndExtraMetadataIsAllowed(): void
    {
        $data = ResponseFixture::data();
        $data['extra'] = ['future' => 'metadata'];
        $data['answers']['intent']['probabilities'] = array_reverse($data['answers']['intent']['probabilities'], true);
        $result = (new JevResponseNormalizer())->normalize(json_encode($data, JSON_THROW_ON_ERROR), 12.5);
        self::assertInstanceOf(ClassificationSuccess::class, $result);
        self::assertSame(Intent::WHEN, $result->intent);
        self::assertSame(Topic::TECH, $result->topic);
        self::assertSame('jev-1.13.0', $result->model);
        self::assertSame(1.0, $result->intentConfidence);
        self::assertSame(1.0, $result->intentProbabilities['WHEN']);
        self::assertSame(100, $result->inputTokens);
        self::assertSame(20, $result->outputTokens);
        self::assertSame(12.5, $result->latencyMs);
    }

    #[DataProvider('invalidBodies')]
    public function testInvalidBodiesNeverBecomeOtherGeneral(string $body): void
    {
        $result = (new JevResponseNormalizer())->normalize($body, 1.0);
        self::assertInstanceOf(ClassificationFailure::class, $result);
        self::assertSame(FailureReason::INVALID_RESPONSE, $result->reason);
    }

    public static function invalidBodies(): iterable
    {
        return ResponseFixture::malformedBodies();
    }

    public function testRoundedDistributionAndLowConfidenceAreAcceptedWithoutThreshold(): void
    {
        $data = ResponseFixture::data();
        $data['answers']['intent']['probabilities']['WHEN'] = 0.5002;
        $data['answers']['intent']['probabilities']['EXPLAIN'] = 0.5002;
        $data['answers']['intent']['confidence'] = 0.0;
        $result = (new JevResponseNormalizer())->normalize(json_encode($data), 0.0);
        self::assertInstanceOf(ClassificationSuccess::class, $result);
        self::assertSame(0.0, $result->intentConfidence);
    }

    public function testValidOtherGeneralIsSuccess(): void
    {
        $data = ResponseFixture::data();
        foreach (['intent' => 'OTHER', 'topic' => 'GENERAL'] as $dimension => $value) {
            $data['answers'][$dimension]['choice'] = $value;
            foreach ($data['answers'][$dimension]['probabilities'] as $category => $_) {
                $data['answers'][$dimension]['probabilities'][$category] = (int) ($category === $value);
            }
        }
        $result = (new JevResponseNormalizer())->normalize(json_encode($data), 0.0);
        self::assertInstanceOf(ClassificationSuccess::class, $result);
        self::assertSame(Intent::OTHER, $result->intent);
        self::assertSame(Topic::GENERAL, $result->topic);
    }
}
