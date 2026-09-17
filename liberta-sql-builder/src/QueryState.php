<?php

namespace Liberta\Sql;

final class QueryState
{
    public string $table = '';
    public array $select = ['*'];
    public bool $distinct = false;

    /** SQL Fragment */
    public string $join = '';
    public string $where = '';
    public string $groupBy = '';
    public string $having = '';
    public string $orderBy = '';

    /** Pagination */
    public string $limit = '';
    public string $offset = '';

    /** Boolean Logic */
    public string $boolean = 'AND';
    public bool $negate = false;

    /** Bindings */
    public array $bindings = [];
    private int $bindingCount = 0;

    /** @internal used for aggregate normalization (MySQL) */
    public array $groupByColumns = [];
    public array $nonAggregateColumns = [];
    public array $columns = [];

    public bool $isRaw = false;
    public bool $skipNonAggregate = false;

    public function addBinding($value): string
    {
        $placeholder = ':b' . $this->bindingCount++;
        $this->bindings[$placeholder] = $value;
        return $placeholder;
    }

    public function reset(): void
    {
        $this->select = ['*'];
        $this->distinct = false;

        $this->join = '';
        $this->where = '';
        $this->groupBy = '';
        $this->groupByColumns = [];
        $this->having = '';
        $this->orderBy = '';
        $this->limit = '';
        $this->offset = '';

        $this->boolean = 'AND';
        $this->negate = false;

        $this->bindings = [];
        $this->bindingCount = 0;

        $this->isRaw = false;
        $this->skipNonAggregate = false;
        $this->nonAggregateColumns = [];
        $this->columns = [];
    }
}
