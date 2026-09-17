<?php

namespace Liberta\Migration;

use Liberta\Sql\DB;

class AlterTable
{
    public function __construct(
        private string $tableName,
        protected DB $db
    ) {}

    public function addColumn(string $name, string $type): void
    {
        $this->db->statement("ALTER TABLE `{$this->tableName}` ADD COLUMN `{$name}` {$type}");
    }

    public function dropColumn(string $name): void
    {
        $this->db->statement("ALTER TABLE `{$this->tableName}` DROP COLUMN `{$name}`");
    }

    public function renameColumn(string $from, string $to): void
    {
        $this->db->statement("ALTER TABLE `{$this->tableName}` RENAME COLUMN `{$from}` TO `{$to}`");
    }

    public function modifyColumn(string $name, string $type): void
    {
        $this->db->statement("ALTER TABLE `{$this->tableName}` MODIFY COLUMN `{$name}` {$type}");
    }

    public function addIndex(string ...$columns): void
    {
        $name = 'idx_' . $this->tableName . '_' . implode('_', $columns);
        $list = implode('`, `', $columns);
        $this->db->statement("CREATE INDEX `{$name}` ON `{$this->tableName}` (`{$list}`)");
    }

    public function addUnique(string ...$columns): void
    {
        $name = 'uk_' . $this->tableName . '_' . implode('_', $columns);
        $list = implode('`, `', $columns);
        $this->db->statement("CREATE UNIQUE INDEX `{$name}` ON `{$this->tableName}` (`{$list}`)");
    }

    public function dropIndex(string $name): void
    {
        $this->db->statement("DROP INDEX `{$name}` ON `{$this->tableName}`");
    }
}
