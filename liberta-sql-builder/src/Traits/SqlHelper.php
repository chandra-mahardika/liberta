<?php

namespace Liberta\Sql\Traits;

trait SqlHelper
{
    protected string $prefix = '';

    protected function wrapValue($value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return (string) $value;
        }

        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        return '\'' . addcslashes((string) $value, "\000\n\r\\'\"\032") . '\'';
    }

    protected function wrapColumns($columns): string
    {
        return is_array($columns)
            ? implode(', ', $columns)
            : $columns;
    }

    protected function wrapTable($table): string
    {
        return $this->prefix . $table;
    }

    protected function aggregate(string $fn, string $column, ?string $alias): string
    {
        return "{$fn}({$column})" . ($alias ? " AS {$alias}" : '');
    }
}
