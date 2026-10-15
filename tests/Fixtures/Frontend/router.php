<?php

// Isolated browser fixture: no development database or live classification.
declare(strict_types=1);

use App\Domain\Answer\Verdict;
use App\Infrastructure\Persistence\VerdictRepository;
use App\Kernel;
use App\Tests\Integration\BackendPersistenceTest;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

$root = dirname(__DIR__, 3);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
    http_response_code(503);
    exit('Browser tests must intercept submissions.');
}
if (!str_starts_with($path, '/a/')) {
    return require $root.'/public/router.php';
}
require $root.'/config/bootstrap.php';
$kernel = new Kernel('test', true);
$kernel->boot();
$db = BackendPersistenceTest::database();
(new VerdictRepository($db))->insert(new Verdict(
    '10000000-0000-4000-8000-000000000099',
    '<script>synthetic question</script> 🦉',
    'Synthetic saved answer. '.str_repeat('Long answer text. ', 30),
    null, [], '2026-10-15T00:00:00Z',
));
$kernel->getContainer()->get('test.service_container')->set(Connection::class, $db);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
