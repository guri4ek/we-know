<?php

declare(strict_types=1);
namespace App\Tests\Unit;

use App\Domain\Classification\{Intent, Topic, ClassificationSuccess, ClassificationFailure};
use App\Infrastructure\Classification\{JevRubric, JevResponseNormalizer};
use App\Infrastructure\Persistence\CatalogImporter;
use App\Tests\Integration\BackendPersistenceTest;
use PHPUnit\Framework\TestCase;

final class TaxonomyV2ContractTest extends TestCase
{
    public const INTENTS = ['YES_NO','WHEN','DURATION','EXPLAIN','METHOD','WHO','IDENTIFY','DESCRIBE','WHERE','QUANTITY','CHOICE','RECOMMEND','COMPARE','OTHER'];
    public const TOPICS = ['TECH','WORK_STUDY','MONEY','RELATIONSHIPS','HEALTH','FOOD','HOME','NATURE','TRAVEL','SPORTS','ENTERTAINMENT','LANGUAGE','GENERAL'];

    public function testV2PayloadAndEnums(): void
    {
        self::assertSame(self::INTENTS, array_column(Intent::cases(), 'value'));
        self::assertSame(self::TOPICS, array_column(Topic::cases(), 'value'));
        $rubric = new JevRubric();
        self::assertSame(self::INTENTS, array_keys($rubric->intentCriteria()));
        self::assertSame(self::TOPICS, array_keys($rubric->topicCriteria()));
        self::assertSame('2.0.0', JevRubric::VERSION);
        self::assertSame('2.0.0', JevRubric::TAXONOMY_VERSION);
        self::assertNull(Intent::tryFrom('ANY'));
        $payload = $rubric->payload('Recommend a printer?', 'offline');
        self::assertSame(['question'=>'Recommend a printer?'], $payload['state']);
        self::assertStringContainsString('requested outcome', $payload['questions']['intent']['instructions']);
        self::assertStringContainsString('missing referent', $payload['questions']['intent']['instructions']);
    }

    public function testAllV2LabelsNormalizeButLegacyDistributionsDoNot(): void
    {
        $normalizer = new JevResponseNormalizer();
        foreach (self::INTENTS as $intent) {
            foreach (self::TOPICS as $topic) {
                $choice = static function (string $value, array $allowed): array {
                    $p = array_fill_keys($allowed, 0); $p[$value] = 1;
                    return ['type'=>'choice','choice'=>$value,'confidence'=>1,'probabilities'=>$p];
                };
                $body = ['model'=>'offline','answers'=>['intent'=>$choice($intent,self::INTENTS),'topic'=>$choice($topic,self::TOPICS)],'usage'=>['input_tokens'=>1,'output_tokens'=>1]];
                $result = $normalizer->normalize(json_encode($body, JSON_THROW_ON_ERROR), 0);
                self::assertInstanceOf(ClassificationSuccess::class, $result, $intent.'/'.$topic);
            }
        }
        self::assertInstanceOf(ClassificationFailure::class, $normalizer->normalize(file_get_contents(__DIR__.'/../Fixtures/Jev/success.json'), 0));
    }

    public function testFreshSchemaAcceptsNewPairsAndImportRequiresTaxonomy(): void
    {
        $data = json_decode(file_get_contents(__DIR__.'/../Fixtures/Catalog/development-v1.json'), true, 512, JSON_THROW_ON_ERROR);
        $data['version'] = 2;
        $data['answers'][0]['eligibility'][0]['intent'] = 'RECOMMEND';
        $data['answers'][0]['eligibility'][0]['topic'] = 'WORK_STUDY';
        $db = BackendPersistenceTest::database();
        $importer = new CatalogImporter($db);
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
        self::assertSame('RECOMMEND', $db->fetchOne('SELECT intent FROM answer_eligibility ORDER BY id LIMIT 1'));
        $data['version'] = 1;
        $this->expectException(\InvalidArgumentException::class);
        $importer->import(json_encode($data, JSON_THROW_ON_ERROR));
    }
}
