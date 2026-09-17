<?php

namespace Liberta\Grammar;

class PostgresGrammar extends Grammar
{
    public function wrap(string $identifier): string
    {
        return '"' . $identifier . '"';
    }

    public function compileLimit(int $limit, ?int $offset = null): string
    {
        $sql = "LIMIT {$limit}";
        if ($offset !== null) {
            $sql .= " OFFSET {$offset}";
        }
        return $sql;
    }

    public function compileInsert(string $table, array $columns, array $placeholders): string
    {
        return parent::compileInsert($table, $columns, $placeholders) . ' RETURNING *';
    }
}
