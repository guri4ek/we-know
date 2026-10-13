<?php

// Offline seed demonstration. Creates only a new explicitly named file; never loads Dotenv.
declare(strict_types=1);

use App\Application\AnswerQuestion;
use App\Command\ImportCatalogCommand;
use App\Domain\Answer\{AnswerSelector, Question, RandomIndexSource};
use App\Domain\Classification\{ClassificationFailure, ClassificationSuccess, FailureReason, Intent, QuestionClassifier, Topic};
use App\Infrastructure\Persistence\{CatalogImporter, CatalogRepository, ConnectionFactory, MigrationFactory, VerdictRepository};
use Doctrine\Migrations\MigratorConfiguration;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$path = $argv[1] ?? '';
if (dirname($path) !== $root.'/var' || !preg_match('/^answer-seed-v2-[a-zA-Z0-9-]+\.sqlite$/D', basename($path))) {
    throw new InvalidArgumentException('Supply a new absolute var/answer-seed-v2-<name>.sqlite target.');
}
$reservation = @fopen($path, 'x');
if ($reservation === false) {
    throw new RuntimeException('Target exists or cannot be created; refusing overwrite.');
}
fclose($reservation);
$check = static function (bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
};
$db = ConnectionFactory::create('sqlite:///'.$path, 'dev');
$factory = MigrationFactory::create($db);
$factory->getMetadataStorage()->ensureInitialized();
$plan = $factory->getMigrationPlanCalculator()->getPlanUntilVersion($factory->getVersionAliasResolver()->resolveVersionAlias('latest'));
$factory->getMigrator()->migrate($plan, (new MigratorConfiguration())->setAllOrNothing(true));
$catalogFile = $root.'/docs/content/seed-v2/catalog.json';
$catalog = json_decode(file_get_contents($catalogFile), true, 512, JSON_THROW_ON_ERROR);
$importer = new CatalogImporter($db);
$command = new ImportCatalogCommand($importer, $root, 'dev');
$check($command->run(new ArrayInput(['file'=>$catalogFile]), new BufferedOutput()) === 0, 'Catalog command failed.');
$snapshot = static fn (): array => [
    $db->fetchAllAssociative('SELECT * FROM answer ORDER BY id'),
    $db->fetchAllAssociative('SELECT * FROM answer_eligibility ORDER BY id'),
];
$before = $snapshot();
$importer->import(json_encode($catalog, JSON_THROW_ON_ERROR));
$check($before === $snapshot(), 'Repeat import changed identities, times or content.');
$expectedEligibilityCount = array_sum(array_map(static fn (array $a): int => count($a['eligibility']), $catalog['answers']));
$check(count($before[0]) === count($catalog['answers']) && count($before[1]) === $expectedEligibilityCount, 'Stored counts differ.');

// Invalid input and a real mid-write database error must both preserve the full catalog.
$invalid = $catalog;
$invalid['answers'][0]['text'] = 'Uncommitted validation probe';
$invalid['answers'][array_key_last($invalid['answers'])]['eligibility'][0]['topic'] = 'ANY';
try { $importer->import(json_encode($invalid, JSON_THROW_ON_ERROR)); throw new RuntimeException('Invalid catalog accepted.'); }
catch (InvalidArgumentException) { $check($before === $snapshot(), 'Validation was not atomic.'); }
$db->executeStatement("CREATE TRIGGER seed_atomicity_probe BEFORE UPDATE ON answer WHEN NEW.text = 'Uncommitted database probe' BEGIN SELECT RAISE(ABORT, 'offline test'); END");
$invalid = $catalog;
$invalid['answers'][0]['text'] = 'Uncommitted earlier update';
$invalid['answers'][1]['text'] = 'Uncommitted database probe';
try { $importer->import(json_encode($invalid, JSON_THROW_ON_ERROR)); throw new RuntimeException('Expected write failure.'); }
catch (\Doctrine\DBAL\Exception) { $check($before === $snapshot(), 'Database rollback was not atomic.'); }
finally { $db->executeStatement('DROP TRIGGER seed_atomicity_probe'); }

$random = new class implements RandomIndexSource {
    public int $index = 0;
    public int $lastCount = 0;
    public function pick(int $count): int { $this->lastCount = $count; return $this->index; }
};
$repository = new CatalogRepository($db);
$selector = new AnswerSelector($repository, $random);
$success = static fn (Intent $intent, Topic $topic): ClassificationSuccess => new ClassificationSuccess($intent, $topic, 'offline-double', 1, 1, [], [], 0, 0, 0);
$cases = [
    ['EXPLAIN','TECH','exact',2377],
    ['METHOD','TECH','intent_general',2407],
    ['DURATION','FOOD','universal',33],
    ['OTHER','GENERAL','exact',3557],
    [null,null,'universal',3184],
];
$questions = file($root.'/docs/research/questions-v1/questions.txt', FILE_IGNORE_NEW_LINES);
$verdicts = new VerdictRepository($db);
$results = [];
foreach ($cases as [$intent,$topic,$tier,$line]) {
    $result = $intent === null ? new ClassificationFailure(FailureReason::TIMEOUT, 0) : $success(Intent::from($intent), Topic::from($topic));
    $classifier = new class($result) implements QuestionClassifier {
        public int $calls = 0;
        public function __construct(private ClassificationSuccess|ClassificationFailure $result) {}
        public function classify(string $question): ClassificationSuccess|ClassificationFailure { ++$this->calls; return $this->result; }
    };
    $verdict = (new AnswerQuestion($classifier, $selector, $verdicts, $db, 'offline-double', 'offline-double'))->answer(Question::from($questions[$line-1]));
    $check($classifier->calls === 1 && $verdict->decisionContext['selection']['tier'] === $tier, 'Wrong application routing.');
    if ($intent === null) {
        $check(!isset($verdict->decisionContext['classifier']['intent']) && $verdict->decisionContext['selection']['reason'] === 'classification_failed', 'Failure fabricated labels.');
    } elseif ($intent === 'OTHER') {
        $check($verdict->decisionContext['classifier']['intent'] === 'OTHER' && $verdict->decisionContext['selection']['pool']['intent'] === 'OTHER', 'OTHER conflated with failure.');
    }
    $results[] = $verdict;
}

// Check every reachable index, without flaky frequency assertions. Mechanisms must not change ordering/weight.
$poolCounts = [];
foreach ($db->fetchAllAssociative('SELECT intent, topic, COUNT(*) AS n FROM answer_eligibility GROUP BY intent, topic ORDER BY intent, topic') as $pool) {
    $intent = $pool['intent']; $topic = $pool['topic'];
    $classification = $intent === 'ANY' ? new ClassificationFailure(FailureReason::TIMEOUT, 0) : $success(Intent::from($intent), Topic::from($topic));
    $ids = [];
    for ($i=0; $i<(int) $pool['n']; ++$i) {
        $random->index = $i;
        $ids[] = $selector->select($classification)->candidate->answer->id;
        $check($random->lastCount === (int) $pool['n'], 'Incorrect random range.');
    }
    $check(count(array_unique($ids)) === (int) $pool['n'], 'Candidate duplication/omission.');
    $db->beginTransaction();
    try {
        $db->executeStatement("UPDATE answer_eligibility SET humor_mechanism = 'hyperbole' WHERE intent = ? AND topic = ?", [$intent, $topic]);
        foreach ($ids as $i=>$id) {
            $random->index=$i;
            $check($selector->select($classification)->candidate->answer->id === $id, 'Mechanism affected selection.');
        }
    } finally { $db->rollBack(); }
    $poolCounts[$intent.'/'.$topic] = (int) $pool['n'];
}

// Change catalog inside a rolled-back demonstration transaction; original seed is restored exactly.
$original = $results[0];
$db->beginTransaction();
try {
    $changed = $catalog;
    foreach ($changed['answers'] as &$answer) {
        if (in_array($original->answerEligibilityId, array_column($answer['eligibility'], 'id'), true)) {
            $answer['text'] = 'Uncommitted snapshot probe'; $answer['enabled'] = false;
            $answer['eligibility'][0]['humor_mechanism'] = 'hyperbole';
            $removedId = $answer['id'];
        }
    }
    unset($answer);
    $importer->import(json_encode($changed, JSON_THROW_ON_ERROR));
    $check($original == $verdicts->find($original->id), 'Saved snapshot changed after edit/disable.');
    $changed['answers'] = array_values(array_filter($changed['answers'], static fn(array $a): bool => $a['id'] !== $removedId));
    $importer->import(json_encode($changed, JSON_THROW_ON_ERROR));
    $saved = $verdicts->find($original->id);
    $check($saved->answerEligibilityId === null && $saved->answerSnapshot === $original->answerSnapshot && $saved->decisionContext === $original->decisionContext, 'Deletion changed immutable snapshot.');
} finally { $db->rollBack(); }
$check($before === $snapshot(), 'Final catalog differs from complete seed.');
$check((int)$db->fetchOne('PRAGMA foreign_keys') === 1 && $db->fetchAllAssociative('PRAGMA foreign_key_check') === [], 'Foreign key integrity failed.');
$db->close();
$reopened = ConnectionFactory::create('sqlite:///'.$path, 'dev');
$reader = new VerdictRepository($reopened);
foreach ($results as $result) { $check($reader->find($result->id) == $result, 'Durable snapshot round trip failed.'); }
$check((int)$reopened->fetchOne('SELECT COUNT(*) FROM verdict') === 5, 'Unexpected smoke verdict count.');
echo json_encode([
    'recorded_at_utc'=>gmdate('Y-m-d\TH:i:s\Z'), 'database'=>$path, 'catalog_sha256'=>hash_file('sha256',$catalogFile),
    'answers'=>count($before[0]),'eligibilities'=>count($before[1]),'pools'=>$poolCounts,'offline_verdicts'=>5,'network_calls'=>0,
    'checks'=>['existing_migration_and_import_command','validation_atomicity','database_failure_rollback','repeat_import_stable_ids_and_times','stored_counts','exact_intent_general_universal','other_distinct_from_failure','every_index_reachable','mechanism_independence','edit_disable_delete_snapshot_immutability','foreign_keys','reconnect_durability'],
    'uniformity_evidence'=>'Each index addresses one candidate; production uses random_int(0, count - 1). No statistical distribution claim.',
    'postgresql'=>'not_run',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), PHP_EOL;
