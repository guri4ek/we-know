<?php

// Read-only verification of the fixed development target. No Dotenv or provider calls.
declare(strict_types=1);

use App\Infrastructure\Persistence\{ConnectionFactory, MigrationFactory};
use Doctrine\Migrations\MigratorConfiguration;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$path = $root.'/var/we-know.sqlite';
$check = static function (bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
};
$check(is_file($path) && !is_link($path) && realpath(dirname($path)) === dirname($path), 'Expected regular local development database.');
$db = ConnectionFactory::create('sqlite:///'.$path, 'dev');
$db->executeStatement('PRAGMA query_only = ON');
$reference = ConnectionFactory::create('sqlite:///:memory:', 'test');
$factory = MigrationFactory::create($reference);
$factory->getMetadataStorage()->ensureInitialized();
$plan = $factory->getMigrationPlanCalculator()->getPlanUntilVersion($factory->getVersionAliasResolver()->resolveVersionAlias('latest'));
$factory->getMigrator()->migrate($plan, (new MigratorConfiguration())->setAllOrNothing(true));
$schemaSql = "SELECT type, name, tbl_name, sql FROM sqlite_master WHERE name NOT LIKE 'sqlite_%' ORDER BY type, name";
$check($db->fetchAllAssociative($schemaSql) === $reference->fetchAllAssociative($schemaSql), 'Schema differs from fresh current migrations.');
$check($db->fetchFirstColumn('SELECT version FROM doctrine_migration_versions ORDER BY version') === $reference->fetchFirstColumn('SELECT version FROM doctrine_migration_versions ORDER BY version'), 'Migration state differs.');
$catalogPath = $root.'/docs/content/seed-v2/catalog.json';
$catalog = json_decode(file_get_contents($catalogPath), true, flags: JSON_THROW_ON_ERROR);
$answers = $eligibilities = [];
foreach ($catalog['answers'] as $answer) {
    foreach ($answer['eligibility'] as $row) {
        $row['answer_id'] = $answer['id'];
        $eligibilities[$row['id']] = $row;
    }
    unset($answer['eligibility']);
    $answer['enabled'] = (int) $answer['enabled'];
    $answers[$answer['id']] = $answer;
}
ksort($answers); ksort($eligibilities);
$stored = [$db->fetchAllAssociative('SELECT * FROM answer ORDER BY id'), $db->fetchAllAssociative('SELECT * FROM answer_eligibility ORDER BY id')];
$check($stored[0] == array_values($answers) && $stored[1] == array_values($eligibilities), 'Database differs from the complete canonical catalog.');
$check((int) $db->fetchOne('SELECT COUNT(*) FROM verdict') === 0, 'Development database contains verdicts.');
$integrity = $db->fetchOne('PRAGMA integrity_check');
$foreignKeys = $db->fetchAllAssociative('PRAGMA foreign_key_check');
$check($integrity === 'ok' && $foreignKeys === [] && (int) $db->fetchOne('PRAGMA foreign_keys') === 1, 'SQLite integrity failed.');
echo json_encode([
    'recorded_at_utc' => gmdate('Y-m-d\TH:i:s\Z'),
    'database' => $path,
    'sqlite_version' => $db->fetchOne('SELECT sqlite_version()'),
    'php_version' => PHP_VERSION,
    'catalog_sha256' => hash_file('sha256', $catalogPath),
    'stored_catalog_sha256' => hash('sha256', json_encode($stored, JSON_THROW_ON_ERROR)),
    'answers' => count($answers), 'eligibilities' => count($eligibilities), 'verdicts' => 0,
    'integrity_check' => $integrity, 'foreign_key_check' => $foreignKeys,
    'checks' => ['schema_equals_fresh_current_migrations', 'migration_versions_match', 'all_catalog_columns_match', 'zero_verdicts', 'foreign_keys_enabled', 'sqlite_integrity'],
    'network_calls' => 0, 'postgresql' => 'not_run',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), PHP_EOL;
$db->close();
$reference->close();
