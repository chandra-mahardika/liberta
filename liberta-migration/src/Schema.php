<?php

namespace Liberta\Migration;

use Liberta\Sql\DB;

/**
 * Schema builder for use inside Migration::up() and Migration::down().
 *
 * Example:
 *
 *   public function up(DB $db): void
 *   {
 *       $schema = new Schema($db);
 *       $schema->create('users', function ($table) {
 *           $table->id();
 *           $table->string('username', 100)->unique();
 *           $table->string('email', 150)->unique();
 *           $table->string('password_hash', 255);
 *           $table->timestamps();
 *       });
 *   }
 */
class Schema
{
    public function __construct(
        protected DB $db
    ) {}

    public function create(string $name, callable $callback): void
    {
        $table = new Table($name);
        $callback($table);

        $sql = $table->toCreateSql();
        $this->db->statement($sql);
    }

    public function drop(string $name): void
    {
        $this->db->statement("DROP TABLE IF EXISTS `{$name}`");
    }

    public function rename(string $from, string $to): void
    {
        $this->db->statement("RENAME TABLE `{$from}` TO `{$to}`");
    }

    public function table(string $name, callable $callback): void
    {
        $table = new AlterTable($name, $this->db);
        $callback($table);
    }

    public function hasTable(string $name): bool
    {
        $result = $this->db->selectRaw("SHOW TABLES LIKE ?", [$name]);
        return !empty($result);
    }

    public function hasColumn(string $table, string $column): bool
    {
        $result = $this->db->selectRaw("SHOW COLUMNS FROM `{$table}` LIKE ?", [$column]);
        return !empty($result);
    }
}
