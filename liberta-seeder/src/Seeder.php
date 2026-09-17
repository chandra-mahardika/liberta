<?php

namespace Liberta\Seeder;

use Liberta\Sql\DB;

interface Seeder
{
    /**
     * Unique seeder name.
     */
    public function name(): string;

    /**
     * Run the seeder.
     */
    public function run(DB $db): void;
}
