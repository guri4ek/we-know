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
        self::assertSelectorExists('form[action="/answer"] textarea[name="question"][id="question"]');
        self::assertSelectorExists('label[for="question"]');
        self::assertSelectorExists('button[type="submit"][disabled]');
        self::assertSelectorTextContains('noscript', 'JavaScript');
        self::assertSelectorTextContains('main', 'Anyone with the result link');
        self::assertSelectorExists('a[href="/privacy"]');
        self::assertSelectorExists('link[rel="stylesheet"][href="/assets/app.css"]');
        self::assertSelectorExists('script[type="module"][src="/assets/app.js"]');
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

    public function testSubmissionsUseTheDedicatedAnswerEndpoint(): void
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
        self::assertSelectorTextContains('h1', 'Page not found');
        self::assertSelectorExists('a[href="/"]');
        self::assertStringNotContainsString('Stack trace', $client->getResponse()->getContent());
    }
}
