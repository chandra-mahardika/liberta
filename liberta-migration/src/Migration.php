<?php

namespace Liberta\Migration;

use Liberta\Sql\DB;

interface Migration
{
    /**
     * Unique migration name (e.g., "20240101_001_create_users_table").
     */
    public function name(): string;

    /**
     * Run the migration (create/alter tables).
     */
    public function up(DB $db): void;

    /**
     * Reverse the migration (drop/alter tables back).
     */
    public function down(DB $db): void;
}
