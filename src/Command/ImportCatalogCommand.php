<?php

declare(strict_types=1);
namespace App\Command;

use App\Infrastructure\Persistence\CatalogImporter;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:catalog:import', description: 'Atomically replace the answer catalog from versioned JSON')]
final class ImportCatalogCommand extends Command
{
    public function __construct(private readonly CatalogImporter $importer, private readonly string $projectDir, private readonly string $environment)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::OPTIONAL, 'Editorial catalog JSON file');
        $this->addOption('development-fixture', null, InputOption::VALUE_NONE, 'Explicitly load the synthetic development catalog (never production)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $development = $input->getOption('development-fixture');
        $file = $input->getArgument('file');
        if (($development && ($file !== null || $this->environment === 'prod')) || (!$development && $file === null)) {
            $output->writeln('Specify an editorial JSON file OR --development-fixture outside production.');
            return self::INVALID;
        }
        $fixtureDir = $this->projectDir.'/tests/Fixtures/Catalog';
        if ($development) {
            $file = $fixtureDir.'/development-v1.json';
        } elseif (str_starts_with(realpath($file) ?: '', $fixtureDir.'/')) {
            $output->writeln('Use --development-fixture to load synthetic content.');
            return self::INVALID;
        }
        if (!is_file($file) || !is_readable($file) || strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'json') {
            $output->writeln('A readable JSON catalog file is required.');
            return self::INVALID;
        }
        $json = file_get_contents($file);
        if ($json === false) {
            $output->writeln('Catalog file could not be read.');
            return self::FAILURE;
        }
        try {
            $this->importer->import($json);
        } catch (\InvalidArgumentException) {
            $output->writeln('Invalid catalog; no changes were committed.');
            return self::FAILURE;
        } catch (Exception) {
            $output->writeln('Catalog persistence failed; no changes were committed.');
            return self::FAILURE;
        }
        $output->writeln($development ? 'Synthetic development catalog imported; editorial acceptance remains outstanding.' : 'Catalog imported.');
        return self::SUCCESS;
    }
}
