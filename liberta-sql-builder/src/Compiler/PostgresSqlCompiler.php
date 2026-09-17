<?php

namespace Liberta\Sql\Compiler;

use Liberta\Sql\QueryState;

final class PostgresSqlCompiler extends BaseCompiler
{
    protected function wrap(string $value): string
    {
        return "\"{$value}\"";
    }

    public function compileSelect(QueryState $state): string
    {
        $distinct = $state->distinct ? 'DISTINCT ' : '';
        $select   = implode(', ', $state->select);

        return trim(sprintf(
            'SELECT %s%s FROM %s %s %s %s %s',
            $distinct,
            $select,
            $state->table,
            $state->join ?? '',
            $state->where ? 'WHERE ' . $state->where : '',
            $state->groupBy,
            $this->compileLimitOffset($state)
        ));
    }

    /**
     * SELECT
     * pakai BaseCompiler (tidak override)
     */

    /**
     * INSERT
     * signature IDENTIK dengan BaseCompiler
     */
    public function compileInsert(
        QueryState $state,
        array $columns,
        array $placeholders
    ): string {
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s) RETURNING *',
            $state->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
    }

    /**
     * UPDATE
     * signature IDENTIK dengan BaseCompiler
     */
    public function compileUpdate(
        QueryState $state,
        array $bindings
    ): string {
        if (!$state->where) {
            throw new \LogicException('UPDATE without WHERE is not allowed');
        }

        $sets = [];
        foreach ($bindings as $column => $placeholder) {
            $sets[] = "{$column} = {$placeholder}";
        }

        return sprintf(
            'UPDATE %s SET %s WHERE %s RETURNING *',
            $state->table,
            implode(', ', $sets),
            $state->where
        );
    }

    /**
     * DELETE
     * signature IDENTIK dengan BaseCompiler
     */
    public function compileDelete(QueryState $state): string
    {
        if (!$state->where) {
            throw new \LogicException('DELETE without WHERE is not allowed');
        }

        return sprintf(
            'DELETE FROM %s WHERE %s RETURNING *',
            $state->table,
            $state->where
        );
    }
}
