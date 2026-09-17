<?php

namespace Liberta\Sql\Compiler;

use Liberta\Sql\QueryState;

final class SQLiteCompiler extends BaseCompiler
{
    protected function wrap(string $value): string
    {
        return "\"{$value}\"";
    }

    public function compileSelect(QueryState $state): string
    {
        $select = implode(', ', $state->select);

        return trim(sprintf(
            'SELECT %s FROM %s %s %s %s',
            $select,
            $state->table,
            $state->join ?? '',
            $state->where ? 'WHERE ' . $state->where : '',
            $this->compileLimitOffset($state)
        ));
    }
}

