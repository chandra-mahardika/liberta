<?php

namespace Liberta\Grammar;

class SqliteGrammar extends Grammar
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
}
