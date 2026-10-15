<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Domain\Classification\QuestionClassifier;
use App\Tests\Integration\BackendPersistenceTest;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FrontendPagesTest extends WebTestCase
{
    #[DataProvider('pages')]
    public function testInformationalPagesAreReadableWithoutJavaScript(string $path, string $heading, string $disclosure): void
    {
        $client = self::createClient();
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->expects(self::never())->method('classify');
        self::getContainer()->set(QuestionClassifier::class, $classifier);
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $heading);
        self::assertSelectorTextContains('main', $disclosure);
        self::assertSelectorExists('a[href="/"]');
        self::assertSelectorCount(1, 'main');
    }

    public static function pages(): iterable
    {
        yield ['/about', 'Certainty is our specialty.', 'prepared in advance'];
        yield ['/privacy', 'Your question. Your call.', 'Anyone with a result link'];
    }

    public function testMissingResultUsesStyled404AtTheOriginalUrl(): void
    {
        $client = self::createClient();
        $client->request('GET', '/a/not-a-result');

        self::assertResponseStatusCodeSame(404);
        self::assertSelectorTextContains('h1', 'Result not found');
        self::assertResponseHeaderSame('X-Robots-Tag', 'noindex, nofollow');
        self::assertResponseHeaderSame('Cache-Control', 'no-store, private');
        self::assertResponseHeaderSame('Content-Type', 'text/html; charset=UTF-8');
        self::assertFalse($client->getResponse()->isRedirection());
    }

    public function testUnavailableResultDoesNotExposeDatabaseDetails(): void
    {
        $client = self::createClient();
        $db = BackendPersistenceTest::database();
        $db->executeStatement('DROP TABLE verdict');
        self::getContainer()->set(Connection::class, $db);
        $client->request('GET', '/a/10000000-0000-4000-8000-000000000099');

        self::assertResponseStatusCodeSame(503);
        self::assertSelectorTextContains('h1', 'Temporarily unavailable');
        self::assertSelectorExists('a[href="/"]');
        self::assertStringNotContainsString('SQLSTATE', $client->getResponse()->getContent());
        self::assertStringNotContainsString('Stack trace', $client->getResponse()->getContent());
        self::assertResponseHeaderSame('Cache-Control', 'no-store, private');
    }
}
