<?php

namespace Liberta\Sql\Compiler;

use Liberta\Sql\QueryState;

final class SqlServerCompiler extends BaseCompiler
{
    protected function wrap(string $value): string
    {
        return "[{$value}]";
    }

    public function compileSelect(QueryState $state): string
    {
        $distinct = $state->distinct ? 'DISTINCT ' : '';
        $select   = implode(', ', $state->select);

        $orderBy = $state->orderBy ?: 'ORDER BY (SELECT NULL)';

        return trim(sprintf(
            'SELECT %s%s FROM %s %s %s %s %s %s',
            $distinct,
            $select,
            $state->table,
            $state->join ?? '',
            $state->where ? 'WHERE ' . $state->where : '',
            $state->groupBy,
            $orderBy,
            $this->compileLimitOffset($state)
        ));
    }

    protected function compileLimitOffset(QueryState $state): string
    {
        if ($state->limit === null) {
            return '';
        }

        $offset = $state->offset ?? 0;

        return sprintf(
            'OFFSET %d ROWS FETCH NEXT %d ROWS ONLY',
            $offset,
            $state->limit
        );
    }

    public function compileInsert(
        QueryState $state,
        array $columns,
        array $placeholders
    ): string {
        return sprintf(
            'INSERT INTO %s (%s) OUTPUT INSERTED.* VALUES (%s)',
            $state->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
    }

    public function compileUpdate(QueryState $state, array $bindings): string
    {
        if (!$state->where) {
            throw new \LogicException('UPDATE without WHERE is not allowed');
        }

        $sets = [];
        foreach ($bindings as $column => $placeholder) {
            $sets[] = "{$column} = {$placeholder}";
        }

        return sprintf(
            'UPDATE %s SET %s OUTPUT INSERTED.* WHERE %s',
            $state->table,
            implode(', ', $sets),
            $state->where
        );
    }

    public function compileDelete(QueryState $state): string
    {
        if (!$state->where) {
            throw new \LogicException('DELETE without WHERE is not allowed');
        }

        return sprintf(
            'DELETE FROM %s OUTPUT DELETED.* WHERE %s',
            $state->table,
            $state->where
        );
    }
}
