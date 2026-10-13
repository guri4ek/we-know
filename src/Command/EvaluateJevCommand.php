<?php

declare(strict_types=1);

namespace App\Command;

use App\Evaluation\CorpusLoader;
use App\Evaluation\EvaluationRunner;
use App\Infrastructure\Classification\JevClassifier;
use App\Infrastructure\Classification\JevResponseNormalizer;
use App\Infrastructure\Classification\JevRubric;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[AsCommand(name: 'app:jev:evaluate', description: 'Evaluate versioned corpus with offline fixtures, or explicitly opt in to bounded live calls.')]
final class EvaluateJevCommand extends Command
{
    public function __construct(
        private readonly CorpusLoader $loader,
        private readonly EvaluationRunner $runner,
        private readonly JevRubric $rubric,
        private readonly JevResponseNormalizer $normalizer,
        private readonly string $projectDir,
        private readonly string $model,
        #[\SensitiveParameter] private readonly string $apiKey,
        private readonly float $timeout = JevClassifier::DEFAULT_TIMEOUT,
        private readonly float $maxDuration = JevClassifier::DEFAULT_MAX_DURATION,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('live', null, InputOption::VALUE_NONE, 'Allow paid outbound calls using JEV_API_KEY.')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Explicit maximum calls; required for live, 1–1000.')
            ->addOption('split', null, InputOption::VALUE_REQUIRED, 'development or holdout', 'development')
            ->addOption('corpus', null, InputOption::VALUE_REQUIRED, 'Corpus JSON; must match the active taxonomy/rubric.')
            ->addOption('offline-fixtures', null, InputOption::VALUE_REQUIRED, 'Replay JSON response fixtures (default: bundled development fixtures).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $live = $input->getOption('live');
            $split = $input->getOption('split');
            if (!in_array($split, ['development', 'holdout'], true)) {
                throw new \InvalidArgumentException('Split must be development or holdout.');
            }
            $limitOption = $input->getOption('limit');
            if ($limitOption !== null && (!is_string($limitOption) || !preg_match('/^[1-9][0-9]{0,3}$/D', $limitOption) || (int) $limitOption > 1000)) {
                throw new \InvalidArgumentException('Limit must be an integer between 1 and 1000.');
            }
            if ($live && ($limitOption === null || trim($this->apiKey) === '' || trim($this->model) === '' || preg_match('/[\r\n]/', $this->apiKey))) {
                throw new \InvalidArgumentException('Live requires --limit, configured JEV_MODEL and JEV_API_KEY.');
            }
            if ($live && $input->getOption('offline-fixtures') !== null) {
                throw new \InvalidArgumentException('Choose live or offline fixtures.');
            }
            if (!is_finite($this->timeout) || !is_finite($this->maxDuration) || $this->timeout <= 0 || $this->maxDuration < $this->timeout) {
                throw new \InvalidArgumentException('Timeout must be positive and max duration must be at least timeout.');
            }
            $corpus = $this->loader->load($input->getOption('corpus') ?? $this->projectDir.'/docs/evaluation/corpus-v2/'.$split.'.json', $split);
            $corpus['total_sample_count'] = count($corpus['samples']);
            if ($live) {
                $client = HttpClient::create();
                $key = $this->apiKey;
            } else {
                $path = $input->getOption('offline-fixtures') ?? $this->projectDir.'/tests/Fixtures/Jev/evaluation-v2.json';
                if (!is_file($path) || !is_readable($path)) {
                    throw new \InvalidArgumentException('Offline fixtures are not readable.');
                }
                try {
                    $fixtures = json_decode(file_get_contents($path), true, 32, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    throw new \InvalidArgumentException('Offline fixtures must be valid JSON.');
                }
                if (!is_array($fixtures) || ($fixtures['taxonomy_version'] ?? null) !== JevRubric::TAXONOMY_VERSION
                    || ($fixtures['rubric_version'] ?? null) !== JevRubric::VERSION || ($fixtures['corpus_version'] ?? null) !== $corpus['version']
                    || ($fixtures['split'] ?? null) !== $split || !is_array($fixtures['responses'] ?? null) || $fixtures['responses'] === []
                ) {
                    throw new \InvalidArgumentException('Offline fixtures do not match corpus version/split.');
                }
                $samples = [];
                $responses = [];
                $byId = array_column($corpus['samples'], null, 'id');
                foreach ($fixtures['responses'] as $id => $fixture) {
                    if (!isset($byId[$id]) || !is_array($fixture) || !is_int($fixture['status'] ?? null)
                        || $fixture['status'] < 100 || $fixture['status'] > 599 || !is_string($fixture['body'] ?? null)
                    ) {
                        throw new \InvalidArgumentException('Invalid offline response fixture.');
                    }
                    $samples[] = $byId[$id];
                    $responses[] = new MockResponse($fixture['body'], ['http_code' => $fixture['status']]);
                }
                $corpus['samples'] = $samples;
                $client = new MockHttpClient($responses);
                $key = 'offline-fixture-key';
            }
            if ($limitOption !== null) {
                $corpus['samples'] = array_slice($corpus['samples'], 0, (int) $limitOption);
            }
            $classifier = new JevClassifier($client, $this->rubric, $this->normalizer, $this->model, $key, $this->timeout, $this->maxDuration);
            $report = $this->runner->run($corpus, $classifier, $this->model, $live ? 'live' : 'offline_fixture_replay');
            $report['request_limit'] = $limitOption === null ? count($corpus['samples']) : (int) $limitOption;
            $report['retry_count'] = 0;
            $report['http_policy'] = ['timeout_seconds' => $this->timeout, 'max_duration_seconds' => $this->maxDuration, 'max_redirects' => 0];
            $output->writeln(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

            return $live && $report['technical_failure_rate']['numerator'] > 0 ? Command::FAILURE : Command::SUCCESS;
        } catch (\InvalidArgumentException $exception) {
            $output->writeln('<error>'.$exception->getMessage().'</error>');

            return Command::INVALID;
        }
    }
}
