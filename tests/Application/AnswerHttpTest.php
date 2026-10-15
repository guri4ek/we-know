<?php

declare(strict_types=1);
namespace App\Tests\Application;

use App\Domain\Answer\RandomIndexSource;
use App\Domain\Classification\{ClassificationFailure, FailureReason, QuestionClassifier};
use App\Http\TrustedOrigin;
use App\Infrastructure\Persistence\CatalogImporter;
use App\Infrastructure\Persistence\ConnectionFactory;
use App\Tests\Integration\{BackendPersistenceTest, CatalogImportTest};
use App\Tests\Unit\AnswerSelectorTest;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Dotenv\Dotenv;

final class AnswerHttpTest extends WebTestCase
{
    private function client(int $calls = 0, bool $failure = false, ?int $randomCalls = null, ?Connection $database = null): array
    {
        $client = self::createClient();
        $client->disableReboot();
        $db = $database ?? BackendPersistenceTest::database();
        (new CatalogImporter($db))->import(json_encode(CatalogImportTest::fixture(), JSON_THROW_ON_ERROR));
        self::getContainer()->set(Connection::class, $db);
        $classifier = $this->createMock(QuestionClassifier::class);
        $classifier->expects(self::exactly($calls))->method('classify')->willReturn($failure ? new ClassificationFailure(FailureReason::TIMEOUT, 800) : AnswerSelectorTest::success());
        self::getContainer()->set(QuestionClassifier::class, $classifier);
        $random = $this->createMock(RandomIndexSource::class);
        $random->expects(self::exactly($randomCalls ?? $calls))->method('pick')->willReturn(0);
        self::getContainer()->set(RandomIndexSource::class, $random);

        return [$client, $db];
    }

    private function post(KernelBrowser $client, string $body, array $headers = []): void
    {
        $client->request('POST', '/answer', [], [], $headers + ['CONTENT_TYPE'=>'application/json', 'HTTP_ORIGIN'=>'http://localhost', 'REMOTE_ADDR'=>'127.0.0.1'], $body);
    }

    public function testDefaultLocalOriginAcceptsLocalhostWhileRejectingCrossOriginRequests(): void
    {
        [$client, $db] = $this->client(1);
        // Exercise shipped defaults rather than the test-only origin override.
        $defaults = (new Dotenv())->parse(file_get_contents(dirname(__DIR__, 2).'/.env'));
        self::getContainer()->set(TrustedOrigin::class, new TrustedOrigin($defaults['APP_PUBLIC_ORIGIN']));
        $headers = ['HTTP_HOST' => 'localhost:8000', 'HTTP_ORIGIN' => 'http://localhost:8000'];

        $this->post($client, '{"question":"A synthetic localhost question"}', $headers);
        self::assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ([
            ['HTTP_ORIGIN' => 'http://127.0.0.1:8000'],
            ['HTTP_HOST' => '127.0.0.1:8000'],
            ['HTTP_ORIGIN' => 'https://evil.example'],
            ['HTTP_SEC_FETCH_SITE' => 'cross-site'],
        ] as $mismatch) {
            $this->post($client, '{"question":"A synthetic blocked question"}', $mismatch + $headers);
            self::assertResponseStatusCodeSame(403);
        }
        self::assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));

        $client->request('GET', $data['url']);
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('link[rel="canonical"][href="http://localhost:8000'.$data['url'].'"]');
    }

    public function testCreatesOnlyAfterCommitAndReadsEscapedSnapshotWithoutProvider(): void
    {
        [$client, $db] = $this->client(1);
        $this->post($client, json_encode(['question'=>' <script>alert("x")</script> 🦉 '], JSON_THROW_ON_ERROR));
        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('Cache-Control', 'no-store, private');
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['id','question','answer','url'], array_keys($data));
        self::assertSame('/a/'.$data['id'], $data['url']);
        self::assertResponseHeaderSame('Location', $data['url']);
        self::assertFalse($db->isTransactionActive());
        self::assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
        $client->request('GET', $data['url'], [], [], ['HTTP_HOST'=>'hostile.example']);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('main', $data['question']);
        self::assertSelectorTextContains('main', $data['answer']);
        self::assertSelectorNotExists('script:not([src])');
        self::assertSelectorExists('[data-verdict] [data-copy-link]');
        self::assertSelectorExists('[data-verdict] a[href="/"]');
        self::assertSelectorExists('link[rel="canonical"][href="http://localhost'.$data['url'].'"]');
        self::assertSelectorExists('meta[name="robots"][content="noindex, nofollow"]');
        self::assertResponseHeaderSame('X-Robots-Tag', 'noindex, nofollow');
        self::assertStringNotContainsString('decision_context', $client->getResponse()->getContent());
        self::assertStringNotContainsString('resolved-model', $client->getResponse()->getContent());
        $client->request('HEAD', $data['url']);
        self::assertResponseIsSuccessful();
        self::assertSame('', $client->getResponse()->getContent());
    }

    #[DataProvider('invalidRequests')]
    public function testInvalidRequestsNeverCallProviderOrPersist(string $body, array $headers, int $status, string $code): void
    {
        [$client, $db] = $this->client();
        $this->post($client, $body, $headers);
        self::assertResponseStatusCodeSame($status);
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame($code, $data['error']['code']);
        self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
        self::assertResponseHeaderSame('Cache-Control', 'no-store, private');
    }

    public static function invalidRequests(): iterable
    {
        yield 'invalid json' => ['{',[],400,'invalid_json'];
        yield 'invalid utf8' => ["{\"question\":\"\xff\"}",[],400,'invalid_json'];
        yield 'array' => ['[]',[],422,'invalid_question'];
        yield 'scalar' => ['"question"',[],422,'invalid_question'];
        yield 'null' => ['{"question":null}',[],422,'invalid_question'];
        yield 'number' => ['{"question":42}',[],422,'invalid_question'];
        yield 'empty' => ['{"question":"  "}',[],422,'invalid_question'];
        yield 'extra' => ['{"question":"q","id":"fake"}',[],422,'invalid_question'];
        yield 'nul' => ['{"question":"q\u0000"}',[],422,'invalid_question'];
        yield 'long' => [json_encode(['question'=>str_repeat('🦉',501)]),[],422,'invalid_question'];
        yield 'media' => ['{"question":"q"}',['CONTENT_TYPE'=>'text/plain'],415,'unsupported_media_type'];
        yield 'charset' => ['{"question":"q"}',['CONTENT_TYPE'=>'application/json; charset=latin1'],415,'unsupported_media_type'];
        yield 'origin' => ['{"question":"q"}',['HTTP_ORIGIN'=>'https://evil.example'],403,'forbidden_origin'];
        yield 'origin missing' => ['{"question":"q"}',['HTTP_ORIGIN'=>''],403,'forbidden_origin'];
        yield 'null origin' => ['{"question":"q"}',['HTTP_ORIGIN'=>'null'],403,'forbidden_origin'];
        yield 'host' => ['{"question":"q"}',['HTTP_HOST'=>'evil.example'],403,'forbidden_origin'];
        yield 'fetch' => ['{"question":"q"}',['HTTP_SEC_FETCH_SITE'=>'cross-site'],403,'forbidden_origin'];
        yield 'size' => [str_repeat(' ',8193),[],413,'body_too_large'];
        yield 'content length' => ['{}',['CONTENT_LENGTH'=>'8193'],413,'body_too_large'];
    }

    public function testUnicodeAndBodyBoundary(): void
    {
        [$client] = $this->client(2);
        $this->post($client, json_encode(['question'=>str_repeat('🦉',500)], JSON_UNESCAPED_UNICODE));
        self::assertResponseStatusCodeSame(201);
        $body = '{"question":"x"}';
        $this->post($client, $body.str_repeat(' ',8192-strlen($body)));
        self::assertResponseStatusCodeSame(201);
    }

    public function testProviderFailureCreatesPersistedUniversalFallback(): void
    {
        [$client, $db] = $this->client(1, true);
        $this->post($client, '{"question":"Hello"}');
        self::assertResponseStatusCodeSame(201);
        $context = json_decode($db->fetchOne('SELECT decision_context FROM verdict'), true);
        self::assertSame('classification_failed', $context['selection']['reason']);
        self::assertArrayNotHasKey('intent', $context['classifier']);
        self::assertArrayNotHasKey('resolved_model', $context['classifier']);
    }

    public function testRateLimitAndUntrustedForwardedIpCannotBypassIt(): void
    {
        [$client, $db] = $this->client(3);
        for ($i=0; $i<3; ++$i) {
            $this->post($client, '{"question":"When?"}', ['HTTP_X_FORWARDED_FOR'=>'192.0.2.'.($i+1)]);
            self::assertResponseStatusCodeSame(201);
        }
        $this->post($client, '{"question":"When?"}', ['HTTP_X_FORWARDED_FOR'=>'192.0.2.99']);
        self::assertResponseStatusCodeSame(429);
        self::assertGreaterThanOrEqual(1, (int) $client->getResponse()->headers->get('Retry-After'));
        self::assertSame(3, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
    }

    public function testPersistenceErrorIsControlledEvenWithDebugEnabled(): void
    {
        [$client, $db] = $this->client(1);
        $db->executeStatement("CREATE TRIGGER reject_http BEFORE INSERT ON verdict BEGIN SELECT RAISE(ABORT, 'private DSN password synthetic'); END");
        $this->post($client, '{"question":"When?"}');
        self::assertResponseStatusCodeSame(503);
        self::assertFalse($client->getResponse()->headers->has('Location'));
        self::assertSame(['error'=>['code'=>'answer_unavailable','message'=>'An answer is temporarily unavailable.']], json_decode($client->getResponse()->getContent(), true));
        self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM verdict'));
    }

    public function testEmptyContentIsControlled(): void
    {
        [$client, $db] = $this->client(1, false, 0);
        $db->executeStatement('DELETE FROM answer');
        $this->post($client, '{"question":"When?"}');
        self::assertResponseStatusCodeSame(503);
        self::assertFalse($client->getResponse()->headers->has('Location'));
    }

    public function testInvalidAndUnknownPermalinksAre404(): void
    {
        [$client] = $this->client();
        foreach (['invalid', '10000000-0000-1000-8000-000000000001', '10000000-0000-4000-8000-000000000099'] as $id) {
            $client->request('GET', '/a/'.$id);
            self::assertResponseStatusCodeSame(404);
        }
    }

    public function testStoredResultSurvivesKernelRebootAndASeparateConnection(): void
    {
        $path = sys_get_temp_dir().'/we-know-http-'.bin2hex(random_bytes(8)).'.sqlite';
        try {
            $db = BackendPersistenceTest::database(ConnectionFactory::create('sqlite:///'.$path, 'test'));
            [$client] = $this->client(1, false, null, $db);
            $this->post($client, '{"question":"When?"}');
            self::assertResponseStatusCodeSame(201);
            $data = json_decode($client->getResponse()->getContent(), true);
            $db->close();
            self::ensureKernelShutdown();
            $reader = self::createClient();
            self::getContainer()->set(Connection::class, ConnectionFactory::create('sqlite:///'.$path, 'test'));
            $classifier = $this->createMock(QuestionClassifier::class);
            $classifier->expects(self::never())->method('classify');
            self::getContainer()->set(QuestionClassifier::class, $classifier);
            $random = $this->createMock(RandomIndexSource::class);
            $random->expects(self::never())->method('pick');
            self::getContainer()->set(RandomIndexSource::class, $random);
            $reader->request('GET', $data['url']);
            self::assertResponseIsSuccessful();
            self::assertSelectorTextContains('main', $data['question']);
            self::assertSelectorTextContains('main', $data['answer']);
        } finally {
            self::ensureKernelShutdown();
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
