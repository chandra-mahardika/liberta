<?php

namespace Liberta\Migration;

use Liberta\Sql\DB;
use Liberta\Log\Logger;
use Liberta\Log\NullLogger;

class Migrator
{
    /** @var Migration[] Indexed by name */
    private array $migrations = [];

    public function __construct(
        protected DB $db,
        protected ?Logger $logger = null
    ) {
        $this->logger ??= new NullLogger();
        $this->ensureMigrationsTable();
    }

    public function register(Migration $migration): void
    {
        $this->migrations[$migration->name()] = $migration;
    }

    /**
     * Register multiple migrations.
     *
     * @param Migration[] $migrations
     */
    public function registerMany(array $migrations): void
    {
        foreach ($migrations as $migration) {
            $this->register($migration);
        }
    }

    /**
     * Run all pending migrations.
     */
    public function migrate(): array
    {
        $applied = $this->getApplied();
        $pending = [];

        foreach ($this->migrations as $name => $migration) {
            if (in_array($name, $applied, true)) {
                continue;
            }

            $this->logger->info("Running migration: {$name}");
            $migration->up($this->db);
            $this->recordMigration($name);
            $pending[] = $name;
        }

        return $pending;
    }

    /**
     * Rollback the last N migrations.
     */
    public function rollback(int $steps = 1): array
    {
        $applied = $this->getApplied();
        $rolledBack = [];

        $toRollback = array_reverse(array_slice($applied, -$steps));

        foreach ($toRollback as $name) {
            if (!isset($this->migrations[$name])) {
                throw new \RuntimeException("Migration [{$name}] not found in registered migrations");
            }

            $this->logger->info("Rolling back migration: {$name}");
            $this->migrations[$name]->down($this->db);
            $this->removeMigration($name);
            $rolledBack[] = $name;
        }

        return $rolledBack;
    }

    /**
     * Get list of applied migration names.
     *
     * @return string[]
     */
    public function getApplied(): array
    {
        $rows = $this->db->table('migrations')
            ->orderBy('applied_at', 'ASC')
            ->pluck('name');

        return $rows;
    }

    /**
     * Get list of pending migration names.
     *
     * @return string[]
     */
    public function getPending(): array
    {
        $applied = $this->getApplied();

        return array_values(array_filter(
            array_keys($this->migrations),
            fn ($name) => !in_array($name, $applied, true)
        ));
    }

    /**
     * Get migration status.
     *
     * @return array<int, array{name: string, status: string}>
     */
    public function status(): array
    {
        $applied = $this->getApplied();
        $status = [];

        foreach ($this->migrations as $name => $migration) {
            $status[] = [
                'name'   => $name,
                'status' => in_array($name, $applied, true) ? 'applied' : 'pending',
            ];
        }

        return $status;
    }

    private function ensureMigrationsTable(): void
    {
        $this->db->statement(
            "CREATE TABLE IF NOT EXISTS `migrations` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uk_migrations_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    private function recordMigration(string $name): void
    {
        $this->db->table('migrations')->insert(['name' => $name]);
    }

    private function removeMigration(string $name): void
    {
        $this->db->table('migrations')->where('name', '=', $name)->delete();
    }
}
