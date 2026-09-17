<?php

/**
 * MySQLCompiler
 *
 * Reference implementation (v1-stable).
 * Other SQL dialect compilers MUST follow
 * the same semantic output as this compiler.
 */

namespace Liberta\Sql\Compiler;

use Liberta\Sql\QueryState;

final class MysqlCompiler extends BaseCompiler
{
    protected function wrap(string $value): string
    {
        return "`{$value}`";
    }

    public function compileSelect(QueryState $state): string
    {
        // DISTINCT
        $distinct = $state->distinct ? 'DISTINCT ' : '';

        // Tambahkan kolom non-aggregate ke GROUP BY jika belum ada
        $groupBy = $state->groupByColumns;
        
        if (!$state->skipNonAggregate) {
            foreach ($state->nonAggregateColumns as $col) {
                if (!in_array($col, $groupBy, true)) {
                    $groupBy[] = $col;
                }
            }
        }
        
        $groupBySql = $groupBy ? 'GROUP BY ' . implode(', ', $groupBy) : '';

        $select = implode(', ', $state->select);

        return trim(sprintf(
            'SELECT %s%s FROM %s%s %s %s %s %s',
            $distinct,
            $select,
            $state->table,
            $state->join ?? '',
            $state->where ? 'WHERE ' . $state->where : '',
            $groupBySql,
            $state->having ? 'HAVING ' . $state->having : '',
            trim("{$state->orderBy} {$state->limit} {$state->offset}")
        ));
    }

    public function compileInsert(
        QueryState $state,
        array $columns,
        array $placeholders
    ): string {
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $state->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
    }
    
    public function compileUpdate(QueryState $state, array $data): string
    {
        if (empty($state->where)) {
            throw new \LogicException('UPDATE without WHERE is not allowed');
        }

        $sets = [];
        foreach ($data as $column => $placeholder) {
            $sets[] = "{$column} = {$placeholder}";
        }

        $setClause = implode(', ', $sets);

        return sprintf(
            'UPDATE %s SET %s WHERE %s',
            $state->table,
            $setClause,
            $state->where
        );
    }

    public function compileDelete(QueryState $state): string
    {
        if (empty($state->where)) {
            throw new \LogicException('DELETE without WHERE is not allowed');
        }

        return sprintf(
            'DELETE FROM %s WHERE %s',
            $state->table,
            $state->where
        );
    }

    public function compileExists(QueryState $state): string
    {
        return sprintf(
            'SELECT 1 FROM (%s) as _exists LIMIT 1',
            $this->compileSelect($state)
        );
    }

    protected function compileLimitOffset(QueryState $state): string
    {
        if ($state->limit === null) {
            return '';
        }

        if ($state->offset !== null) {
            return " LIMIT {$state->offset}, {$state->limit}";
        }

        return " LIMIT {$state->limit}";
    }
}
