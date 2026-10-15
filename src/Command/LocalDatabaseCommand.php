<?php

declare(strict_types=1);

namespace App\Command;

use App\Infrastructure\Persistence\LocalDatabase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:db', description: 'Reset, migrate, or seed the disposable local development database')]
final class LocalDatabaseCommand extends Command
{
    public function __construct(private readonly LocalDatabase $database) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('action', InputArgument::REQUIRED, 'reset, migrate, or seed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');
        if (!in_array($action, ['reset', 'migrate', 'seed'], true)) {
            $output->writeln('Choose reset, migrate, or seed.');
            return self::INVALID;
        }
        try {
            $this->database->$action();
        } catch (\Throwable) {
            // Do not echo connection URLs, environment contents, or driver exceptions.
            $output->writeln('Local database '.$action.' failed. Check APP_ENV=dev, the default SQLite target, regular files, and the canonical catalog.');
            return self::FAILURE;
        }
        $output->writeln('Local database '.$action.' completed.');
        return self::SUCCESS;
    }
}
