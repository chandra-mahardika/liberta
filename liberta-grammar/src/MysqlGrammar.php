<?php

namespace Liberta\Grammar;

class MysqlGrammar extends Grammar
{
    public function wrap(string $identifier): string
    {
        return '`' . $identifier . '`';
    }

    public function compileLimit(int $limit, ?int $offset = null): string
    {
        return $offset !== null
            ? "LIMIT {$offset}, {$limit}"
            : "LIMIT {$limit}";
    }

    public function compileExists(string $subquery): string
    {
        return "SELECT EXISTS({$subquery}) AS `exists`";
    }
}
