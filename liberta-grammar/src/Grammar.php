<?php

namespace Liberta\Grammar;

/**
 * Abstract SQL grammar — defines how SQL components are expressed.
 * Each database dialect extends this class.
 */
abstract class Grammar
{
    /**
     * Wrap an identifier (column/table name) with the dialect's quote character.
     */
    abstract public function wrap(string $identifier): string;

    /**
     * Wrap a table name, optionally with a prefix.
     */
    public function wrapTable(string $table, string $prefix = ''): string
    {
        $wrapped = $this->wrap($table);
        return $prefix !== '' ? $prefix . $wrapped : $wrapped;
    }

    /**
     * Quote a literal value for use in SQL.
     */
    public function quoteValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return "'" . addslashes((string) $value) . "'";
    }

    /**
     * Compile LIMIT/OFFSET clause.
     */
    abstract public function compileLimit(int $limit, ?int $offset = null): string;

    /**
     * Compile an aggregate expression.
     */
    public function aggregate(string $function, string $column, ?string $alias = null): string
    {
        $expr = "{$function}({$column})";
        return $alias ? "{$expr} AS {$alias}" : $expr;
    }

    /**
     * Compile a column list for SELECT.
     */
    public function compileColumns(array $columns): string
    {
        return implode(', ', $columns);
    }

    /**
     * Compile a JOIN clause.
     */
    public function compileJoin(string $type, string $table, string $first, string $operator, string $second): string
    {
        return "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
    }

    /**
     * Compile a WHERE expression.
     */
    public function compileWhere(string $column, string $operator, string $placeholder): string
    {
        return "{$column} {$operator} {$placeholder}";
    }

    /**
     * Compile an INSERT statement.
     */
    public function compileInsert(string $table, array $columns, array $placeholders): string
    {
        $cols = implode(', ', array_map(fn ($c) => $this->wrap($c), $columns));
        $vals = implode(', ', $placeholders);

        return "INSERT INTO {$table} ({$cols}) VALUES ({$vals})";
    }

    /**
     * Compile an UPDATE statement.
     */
    public function compileUpdate(string $table, array $set, string $where): string
    {
        $sets = implode(', ', array_map(
            fn ($col, $ph) => "{$this->wrap($col)} = {$ph}",
            array_keys($set),
            array_values($set)
        ));

        return "UPDATE {$table} SET {$sets} WHERE {$where}";
    }

    /**
     * Compile a DELETE statement.
     */
    public function compileDelete(string $table, string $where): string
    {
        return "DELETE FROM {$table} WHERE {$where}";
    }

    /**
     * Compile a CREATE TABLE statement.
     */
    public function compileCreateTable(string $name, array $columns): string
    {
        $body = implode(",\n    ", $columns);
        return "CREATE TABLE IF NOT EXISTS {$name} (\n    {$body}\n)";
    }

    /**
     * Compile a DROP TABLE statement.
     */
    public function compileDropTable(string $name): string
    {
        return "DROP TABLE IF EXISTS {$name}";
    }

    /**
     * Compile an EXISTS subquery for boolean checks.
     */
    public function compileExists(string $subquery): string
    {
        return "SELECT EXISTS({$subquery}) AS _exists";
    }
}
