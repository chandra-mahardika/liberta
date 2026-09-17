<?php

namespace Liberta\Grammar;

class SqlServerGrammar extends Grammar
{
    public function wrap(string $identifier): string
    {
        return '[' . $identifier . ']';
    }

    public function compileLimit(int $limit, ?int $offset = null): string
    {
        if ($offset === null) {
            return "TOP {$limit}";
        }
        return "OFFSET {$offset} ROWS FETCH NEXT {$limit} ROWS ONLY";
    }

    public function compileInsert(string $table, array $columns, array $placeholders): string
    {
        return parent::compileInsert($table, $columns, $placeholders) . ' OUTPUT INSERTED.*';
    }
}
