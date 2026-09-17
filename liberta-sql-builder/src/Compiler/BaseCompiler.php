<?php

namespace Liberta\Sql\Compiler;

use Liberta\Sql\QueryState;

/**
 * BaseCompiler
 *
 * Dialect-neutral SQL compiler base.
 * Concrete compilers (MySQL, PostgreSQL, SQL Server, SQLite)
 * MUST follow the semantic output of MysqlCompiler (v1-stable).
 */
abstract class BaseCompiler implements Compiler
{
    // =============================
    // IDENTIFIER WRAPPING
    // =============================
    protected function wrap(string $value): string
    {
        return $value; // default: no wrapping
    }

    protected function columnize(array $columns): string
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    // =============================
    // LIMIT / OFFSET (NETRAL)
    // =============================
    // protected function compileLimitOffset(QueryState $state): string
    // {
    //     if ($state->limit === null) {
    //         return '';
    //     }

    //     if ($state->offset !== null) {
    //         return " LIMIT {$state->limit} OFFSET {$state->offset}";
    //     }

    //     return " LIMIT {$state->limit}";
    // }

    protected function compileLimitOffset(QueryState $state): string
    {
        if ($state->limit === null) {
            return '';
        }

        if ($state->offset !== null) {
            return sprintf(
                'LIMIT %d OFFSET %d',
                $state->limit,
                $state->offset
            );
        }

        return sprintf('LIMIT %d', $state->limit);
    }

    // =============================
    // SELECT (MUST BE IMPLEMENTED)
    // =============================
    abstract public function compileSelect(QueryState $state): string;

    // =============================
    // INSERT (DEFAULT)
    // =============================
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

    // =============================
    // UPDATE (DEFAULT, SAFE)
    // =============================
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
            'UPDATE %s SET %s WHERE %s',
            $state->table,
            implode(', ', $sets),
            $state->where
        );
    }

    // =============================
    // DELETE (DEFAULT, SAFE)
    // =============================
    public function compileDelete(QueryState $state): string
    {
        if (!$state->where) {
            throw new \LogicException('DELETE without WHERE is not allowed');
        }

        return sprintf(
            'DELETE FROM %s WHERE %s',
            $state->table,
            $state->where
        );
    }

    // =============================
    // EXISTS (DEFAULT, SAFE)
    // =============================
    public function compileExists(QueryState $state): string
    {
        return sprintf(
            'SELECT EXISTS (%s)',
            $this->compileSelect($state)
        );
    }
}
