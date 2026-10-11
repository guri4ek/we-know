<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Classification\ClassificationSuccess;
use App\Domain\Classification\QuestionClassifier;
use App\Infrastructure\Classification\JevClassifier;
use App\Tests\Fixtures\Jev\ResponseFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class JevContainerTest extends KernelTestCase
{
    public function testInterfaceResolvesThroughDiAndDedicatedHttpClient(): void
    {
        self::bootKernel();
        $mock = new MockHttpClient(new MockResponse(ResponseFixture::json()));
        self::getContainer()->set('jev.http_client', $mock);
        $classifier = self::getContainer()->get(QuestionClassifier::class);
        self::assertInstanceOf(JevClassifier::class, $classifier);
        self::assertInstanceOf(ClassificationSuccess::class, $classifier->classify('When?'));
        self::assertSame(1, $mock->getRequestsCount());
    }

    public function testDefaultTestClientDeniesUnplannedNetwork(): void
    {
        self::bootKernel();
        $classifier = self::getContainer()->get(QuestionClassifier::class);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unexpected outbound request');
        $classifier->classify('Question');
    }
}
