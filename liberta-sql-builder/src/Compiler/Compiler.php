<?php

namespace Liberta\Sql\Compiler;

use Liberta\Sql\QueryState;

interface Compiler
{
    public function compileSelect(QueryState $state): string;

    public function compileInsert(
        QueryState $state,
        array $columns,
        array $placeholders
    ): string;

    public function compileUpdate(
        QueryState $state,
        array $data
    ): string;

    public function compileDelete(QueryState $state): string;

    public function compileExists(QueryState $state): string;
}
