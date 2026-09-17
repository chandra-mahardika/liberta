<?php

namespace Liberta\Cli\Commands;

use Liberta\Cli\BaseCommand;
use Liberta\Migration\Migrator;

class MigrateCommand extends BaseCommand
{
    private Migrator $migrator;

    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    public function name(): string
    {
        return 'migrate';
    }

    public function description(): string
    {
        return 'Run pending database migrations';
    }

    public function usage(): string
    {
        return <<<'HELP'
Usage: liberta migrate [options]

Options:
  --rollback[=N]   Rollback last N migrations (default: 1)
  --status         Show migration status
  --force          Skip confirmation prompt

HELP;
    }

    public function execute(array $args): int
    {
        $this->parseArgs($args);

        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('rollback')) {
            return $this->rollback();
        }

        return $this->migrate();
    }

    private function migrate(): int
    {
        $pending = $this->migrator->getPending();

        if (empty($pending)) {
            $this->info('Nothing to migrate. All migrations are applied.');
            return 0;
        }

        $this->info('Pending migrations:');
        foreach ($pending as $name) {
            $this->info("  - {$name}");
        }

        if (!$this->option('force') && !$this->confirm('Run these migrations?')) {
            $this->info('Migration cancelled.');
            return 0;
        }

        $applied = $this->migrator->migrate();

        foreach ($applied as $name) {
            $this->success("Migrated: {$name}");
        }

        $this->info(count($applied) . ' migration(s) applied.');
        return 0;
    }

    private function rollback(): int
    {
        $steps = (int) ($this->option('rollback') ?: 1);

        if (!$this->option('force') && !$this->confirm("Rollback {$steps} migration(s)?")) {
            $this->info('Rollback cancelled.');
            return 0;
        }

        $rolledBack = $this->migrator->rollback($steps);

        foreach ($rolledBack as $name) {
            $this->success("Rolled back: {$name}");
        }

        $this->info(count($rolledBack) . ' migration(s) rolled back.');
        return 0;
    }

    private function showStatus(): int
    {
        $status = $this->migrator->status();

        if (empty($status)) {
            $this->info('No migrations registered.');
            return 0;
        }

        $this->info('Migration status:');
        foreach ($status as $item) {
            $badge = $item['status'] === 'applied' ? '✓' : '○';
            $this->info("  [{$badge}] {$item['name']}");
        }

        return 0;
    }
}
