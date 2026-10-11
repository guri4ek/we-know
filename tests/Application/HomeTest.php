<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeTest extends WebTestCase
{
    public function testHomeIsAnEnglishServerRenderedPage(): void
    {
        $client = self::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'text/html; charset=UTF-8');
        self::assertSelectorExists('html[lang="en"]');
        self::assertSelectorTextContains('h1', "We don't think. We know.");
        self::assertSelectorTextContains('main', 'Coming soon.');
        self::assertSelectorCount(1, 'main');
        self::assertSelectorExists('meta[name="robots"][content="noindex, nofollow"]');
    }

    public function testHeadDoesNotReturnThePageBody(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/');

        self::assertResponseIsSuccessful();
        self::assertSame('', $client->getResponse()->getContent());
    }

    public function testHomeDoesNotAcceptSubmissionsYet(): void
    {
        $client = self::createClient();
        $client->request('POST', '/');

        self::assertResponseStatusCodeSame(405);
        self::assertResponseHeaderSame('Allow', 'GET');
    }

    public function testUnknownPageReturnsNotFound(): void
    {
        $client = self::createClient();
        $client->request('GET', '/not-a-page');

        self::assertResponseStatusCodeSame(404);
    }
}
