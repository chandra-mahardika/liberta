<?php

namespace Liberta\Cli\Commands;

use Liberta\Cli\BaseCommand;
use Liberta\Seeder\SeederRunner;

class SeedCommand extends BaseCommand
{
    private SeederRunner $runner;

    public function __construct(SeederRunner $runner)
    {
        $this->runner = $runner;
    }

    public function name(): string
    {
        return 'seed';
    }

    public function description(): string
    {
        return 'Run database seeders';
    }

    public function usage(): string
    {
        return <<<'HELP'
Usage: liberta seed [seeder_name ...]

Options:
  --force    Skip confirmation prompt
  --list     List registered seeders

HELP;
    }

    public function execute(array $args): int
    {
        $this->parseArgs($args);

        if ($this->option('list')) {
            $names = $this->runner->getRegistered();
            $this->info('Registered seeders:');
            foreach ($names as $name) {
                $this->info("  - {$name}");
            }
            return 0;
        }

        $targetNames = !empty($this->arguments)
            ? $this->arguments
            : [];

        $allNames = $this->runner->getRegistered();
        $toRun = !empty($targetNames)
            ? array_intersect($targetNames, $allNames)
            : $allNames;

        if (empty($toRun)) {
            $this->info('No seeders to run.');
            return 0;
        }

        $this->info('Seeders to run:');
        foreach ($toRun as $name) {
            $this->info("  - {$name}");
        }

        if (!$this->option('force') && !$this->confirm('Run these seeders?')) {
            $this->info('Seeding cancelled.');
            return 0;
        }

        $run = $this->runner->run(array_values($toRun));

        foreach ($run as $name) {
            $this->success("Seeded: {$name}");
        }

        $this->info(count($run) . ' seeder(s) executed.');
        return 0;
    }
}
