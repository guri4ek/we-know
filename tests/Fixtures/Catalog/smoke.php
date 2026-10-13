<?php

// Explicit offline development smoke; does not load Dotenv or credentials.
declare(strict_types=1);

use App\Application\AnswerQuestion;
use App\Domain\Answer\{AnswerSelector, Question, RandomIndexSource};
use App\Domain\Classification\{ClassificationFailure, FailureReason, QuestionClassifier};
use App\Infrastructure\Persistence\{CatalogImporter, CatalogRepository, ConnectionFactory, VerdictRepository};

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$path = $root.'/var/we-know.sqlite';
if (!is_file($path)) {
    throw new RuntimeException('Apply development migrations first.');
}
$db = ConnectionFactory::create('sqlite:///'.$path, 'dev');
(new CatalogImporter($db))->import(file_get_contents(__DIR__.'/development-v2.json'));
$classifier = new class implements QuestionClassifier {
    public int $calls = 0;
    public function classify(string $question): ClassificationFailure
    {
        ++$this->calls;
        return new ClassificationFailure(FailureReason::TIMEOUT, 800);
    }
};
$random = new class implements RandomIndexSource {
    public function pick(int $count): int { return 0; }
};
$repository = new VerdictRepository($db);
$verdict = (new AnswerQuestion($classifier, new AnswerSelector(new CatalogRepository($db), $random), $repository, $db, 'synthetic', 'offline-fixture'))->answer(Question::from('Synthetic offline smoke question 🦉'));
$db->close();
$reader = new VerdictRepository(ConnectionFactory::create('sqlite:///'.$path, 'dev'));
if ($reader->find($verdict->id) != $verdict || $classifier->calls !== 1) {
    throw new RuntimeException('Snapshot smoke failed.');
}
echo json_encode(['database' => $path, 'id' => $verdict->id, 'url' => '/a/'.$verdict->id, 'classification_calls' => $classifier->calls, 'network_calls' => 0, 'snapshot_round_trip' => true, 'catalog' => 'synthetic development only'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), PHP_EOL;
