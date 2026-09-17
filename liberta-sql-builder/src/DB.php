<?php

namespace Liberta\Sql;

use Liberta\Sql\Traits\SqlHelper;
use Liberta\Sql\Compiler\Compiler;
use Liberta\Sql\Compiler\MysqlCompiler;
use Liberta\Sql\Compiler\SqlServerCompiler;
use Liberta\Sql\Compiler\PostgresSqlCompiler;
use Liberta\Sql\Compiler\SQLiteCompiler;
use Liberta\Sql\Executor\Executor;
use Liberta\Sql\Executor\PdoExecutor;

use PDO;

final class DB
{
    use SqlHelper;

    private ?Connection $connection = null;
    private QueryState $state;
    private Compiler $compiler;
    private Executor $executor;
    private PDO $pdo;
    private int $transactionDepth = 0;

    public function __construct(array $config)
    {
        $this->prefix = $config['prefix'] ?? '';
        $this->connection = new Connection($config);
        $this->pdo = $this->connection->pdo();
        $this->init();
    }

    public static function fromPdo(PDO $pdo, string $driver = 'mysql', string $prefix = ''): static
    {
        $ref = new \ReflectionClass(static::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $instance->pdo = $pdo;
        $instance->prefix = $prefix;
        $instance->compiler = match ($driver) {
            'pgsql'   => new PostgresSqlCompiler(),
            'sqlsrv'  => new SqlServerCompiler(),
            'sqlite'  => new SQLiteCompiler(),
            default   => new MysqlCompiler(),
        };
        $instance->executor = new PdoExecutor($pdo);
        $instance->state = new QueryState();

        return $instance;
    }

    private function init(): void
    {
        $this->state = new QueryState();
        $this->executor = new PdoExecutor($this->pdo);
        $this->compiler ??= match ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            'pgsql'   => new PostgresSqlCompiler(),
            'sqlsrv'  => new SqlServerCompiler(),
            'sqlite'  => new SQLiteCompiler(),
            default   => new MysqlCompiler(),
        };
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /* ======================================================
     | Transactions
     ====================================================== */

    public function begin(): bool
    {
        if ($this->transactionDepth === 0) {
            $result = $this->pdo->beginTransaction();
        } else {
            $name = 'sp_' . $this->transactionDepth;
            $this->pdo->exec("SAVEPOINT {$name}");
            $result = true;
        }

        $this->transactionDepth++;
        return $result;
    }

    public function commit(): bool
    {
        if ($this->transactionDepth <= 0) {
            throw new \LogicException('No active transaction to commit');
        }

        $this->transactionDepth--;

        if ($this->transactionDepth === 0) {
            return $this->pdo->commit();
        }

        return true;
    }

    public function rollback(): bool
    {
        if ($this->transactionDepth <= 0) {
            throw new \LogicException('No active transaction to rollback');
        }

        if ($this->transactionDepth === 1) {
            $this->transactionDepth = 0;
            return $this->pdo->rollBack();
        }

        $this->transactionDepth--;
        $name = 'sp_' . $this->transactionDepth;
        $this->pdo->exec("ROLLBACK TO SAVEPOINT {$name}");

        return true;
    }

    public function savepoint(string $name): bool
    {
        $this->pdo->exec("SAVEPOINT {$name}");
        return true;
    }

    public function releaseSavepoint(string $name): bool
    {
        $this->pdo->exec("RELEASE SAVEPOINT {$name}");
        return true;
    }

    public function rollbackToSavepoint(string $name): bool
    {
        $this->pdo->exec("ROLLBACK TO SAVEPOINT {$name}");
        return true;
    }

    public function inTransaction(): bool
    {
        return $this->transactionDepth > 0;
    }

    public function transaction(callable $callback): mixed
    {
        $this->begin();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function raw(string $expression): string
    {
        return $expression;
    }

    public function statement(string $sql, array $bindings = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($bindings);
    }

    /* ======================================================
     | Table
     ====================================================== */

    public function table(string $table, ?string $as = null): self
    {
        $this->state->table = $this->wrapTable($table)
            . ($as ? " AS {$as}" : '');

        return $this;
    }

    private function addAggregate(string $fn, string $field, ?string $alias): self
    {
        if ($this->state->select === ['*']) {
            $this->state->select = [];
        }

        $this->state->select[] = $this->aggregate($fn, $field, $alias);

        return $this;
    }

    public function min(string $field, ?string $alias = null): self
    {
        return $this->addAggregate('MIN', $field, $alias);
    }

    public function max(string $field, ?string $alias = null): self
    {
        return $this->addAggregate('MAX', $field, $alias);
    }

    public function count(string $field = '*', ?string $alias = null): self
    {
        return $this->addAggregate('COUNT', $field, $alias);
    }

    public function avg(string $field, ?string $alias = null): self
    {
        return $this->addAggregate('AVG', $field, $alias);
    }

    public function sum(string $field, ?string $alias = null): self
    {
        return $this->addAggregate('SUM', $field, $alias);
    }

    /* ======================================================
     | Select
     ====================================================== */

    public function select(...$columns): self
    {
        $this->state->select = [];

        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }

        if (count($columns) === 1 && is_string($columns[0])) {
            $columns = array_map('trim', explode(',', $columns[0]));
        }

        foreach ($columns as $column) {
            $this->state->select[] = $column;
        }

        if (empty($this->state->select)) {
            $this->state->select = ['*'];
        }

        return $this;
    }

    public function addSelect(...$columns): self
    {
        if ($this->state->select === ['*']) $this->state->select = [];
        if (count($columns) === 1 && is_array($columns[0])) $columns = $columns[0];

        foreach ($columns as $col) {
            $this->state->select[] = $col;
            if (!preg_match('/^(COUNT|SUM|AVG|MIN|MAX)\(/i', $col)) {
                $this->state->nonAggregateColumns[] = $col;
            }
        }

        return $this;
    }

    public function selectRaw(string $expression): self
    {
        if ($this->state->select === ['*']) {
            $this->state->select = [];
        }

        $this->state->select[] = $expression;
        return $this;
    }

    public function distinct(): self
    {
        $this->state->distinct = true;
        return $this;
    }

    /* ======================================================
     | Joins
     ====================================================== */

    public function join(
        string $table,
        string $first,
        string $operator = '=',
        ?string $second = null,
        string $type = 'INNER'
    ): self {
        $second ??= $operator;
        $operator = $second === $operator ? '=' : $operator;

        $joinTable = $this->wrapTable($table);

        $this->state->join .= sprintf(
            ' %s JOIN %s ON %s %s %s',
            strtoupper($type),
            $joinTable,
            $first,
            $operator,
            $second
        );

        return $this;
    }

    public function innerJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'INNER');
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function leftOuterJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT OUTER');
    }

    public function rightOuterJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT OUTER');
    }

    public function fullOuterJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'FULL OUTER');
    }

    /* ======================================================
     | Where
     ====================================================== */

    private function addWhere(string $expression): void
    {
        $prefix = $this->state->where ? " {$this->state->boolean} " : '';
        $not = $this->state->negate ? 'NOT ' : '';
        $this->state->where .= $prefix . $not . $expression;

        $this->state->boolean = 'AND';
        $this->state->negate = false;
    }

    public function where(string $column, string $operator, $value): self
    {
        $placeholder = $this->state->addBinding($value);
        $this->addWhere("{$column} {$operator} {$placeholder}");
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        $this->state->boolean = 'OR';
        return $this->where($column, $operator, $value);
    }

    public function notWhere(string $column, string $operator, $value): self
    {
        $this->state->negate = true;
        return $this->where($column, $operator, $value);
    }

    public function orNotWhere(string $column, string $operator, $value): self
    {
        $this->state->boolean = 'OR';
        $this->state->negate = true;
        return $this->where($column, $operator, $value);
    }

    public function whereNull(string $column): self
    {
        $this->addWhere("{$column} IS NULL");
        return $this;
    }

    public function orWhereNull(string $column): self
    {
        $this->state->boolean = 'OR';
        return $this->whereNull($column);
    }

    public function whereNotNull(string $column): self
    {
        $this->addWhere("{$column} IS NOT NULL");
        return $this;
    }

    public function orWhereNotNull(string $column): self
    {
        $this->state->boolean = 'OR';
        return $this->whereNotNull($column);
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = [];

        foreach ($values as $value) {
            $placeholders[] = $this->state->addBinding($value);
        }

        $this->addWhere(
            "{$column} IN (" . implode(', ', $placeholders) . ")"
        );

        return $this;
    }

    public function orWhereIn(string $column, array $values): self
    {
        $this->state->boolean = 'OR';
        return $this->whereIn($column, $values);
    }

    public function whereNotIn(string $column, array $values): self
    {
        $this->state->negate = true;
        return $this->whereIn($column, $values);
    }

    public function orWhereNotIn(string $column, array $values): self
    {
        $this->state->boolean = 'OR';
        $this->state->negate = true;
        return $this->whereIn($column, $values);
    }

    public function whereBetween(string $column, $from, $to = null): self
    {
        if (is_array($from)) {
            [$from, $to] = $from;
        }

        $fromPh = $this->state->addBinding($from);
        $toPh   = $this->state->addBinding($to);

        $this->addWhere(
            "{$column} BETWEEN {$fromPh} AND {$toPh}"
        );

        return $this;
    }

    public function orWhereBetween(string $column, $from, $to = null): self
    {
        $this->state->boolean = 'OR';
        return $this->whereBetween($column, $from, $to);
    }

    public function whereNotBetween(string $column, $from, $to = null): self
    {
        $this->state->negate = true;
        return $this->whereBetween($column, $from, $to);
    }

    public function orWhereNotBetween(string $column, $from, $to = null): self
    {
        $this->state->boolean = 'OR';
        $this->state->negate = true;
        return $this->whereBetween($column, $from, $to);
    }

    public function when($condition, callable $callback, ?callable $default = null): self
    {
        if ($condition) {
            $callback($this, $condition);
        } elseif ($default) {
            $default($this, $condition);
        }
        return $this;
    }

    public function like(string $column, string $search): self
    {
        $ph = $this->state->addBinding("%{$search}%");
        $this->addWhere("{$column} LIKE {$ph}");
        return $this;
    }

    public function orLike(string $column, string $search): self
    {
        $this->state->boolean = 'OR';
        return $this->like($column, $search);
    }

    public function notLike(string $column, string $search): self
    {
        $this->state->negate = true;
        return $this->like($column, $search);
    }

    public function orNotLike(string $column, string $search): self
    {
        $this->state->boolean = 'OR';
        $this->state->negate = true;
        return $this->like($column, $search);
    }

    /* ======================================================
     | Group By / Having
     ====================================================== */

    public function groupBy($columns): self
    {
        if (is_string($columns)) $columns = [$columns];
        $this->state->groupByColumns = $columns;
        return $this;
    }

    private function addHaving(string $expression): void
    {
        $prefix = $this->state->having
            ? " {$this->state->boolean} "
            : '';

        $not = $this->state->negate ? 'NOT ' : '';

        $this->state->having .= $prefix . $not . $expression;

        $this->state->boolean = 'AND';
        $this->state->negate = false;
    }

    public function having(string $column, string $operator, $value): self
    {
        $placeholder = $this->state->addBinding($value);
        $this->addHaving("{$column} {$operator} {$placeholder}");
        return $this;
    }

    public function orHaving(string $column, string $operator, $value): self
    {
        $this->state->boolean = 'OR';
        return $this->having($column, $operator, $value);
    }

    public function notHaving(string $column, string $operator, $value): self
    {
        $this->state->negate = true;
        return $this->having($column, $operator, $value);
    }

    public function orNotHaving(string $column, string $operator, $value): self
    {
        $this->state->boolean = 'OR';
        $this->state->negate = true;
        return $this->having($column, $operator, $value);
    }

    /* ======================================================
     | Order / Pagination
     ====================================================== */

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->state->orderBy = "ORDER BY {$column} {$direction}";
        return $this;
    }

    public function orderByAggregate(string $fn, string $column, string $direction = 'ASC'): self
    {
        $this->state->orderBy = sprintf(
            'ORDER BY %s(%s) %s',
            strtoupper($fn),
            $column,
            $direction
        );

        return $this;
    }

    public function limit(int $limit, ?int $offset = null): self
    {
        $this->state->limit = $offset !== null
            ? "LIMIT {$offset}, {$limit}"
            : "LIMIT {$limit}";

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->state->offset = "OFFSET {$offset}";
        return $this;
    }

    public function paginate(int $perPage, int $page = 1): self
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $this->limit($perPage);
        $this->offset($offset);

        return $this;
    }

    public function forPage(int $page, int $perPage): self
    {
        return $this->paginate($perPage, $page);
    }

    /* ======================================================
     | Execution
     ====================================================== */

    public function get(): array
    {
        $sql = $this->compiler->compileSelect($this->state);
        $rows = $this->executor->select($sql, $this->state->bindings);
        $this->state->reset();

        return $rows;
    }

    public function first($columns = ['*'])
    {
        if ($columns) {
            $this->select($columns);
        }

        $this->limit(1);
        $rows = $this->get();

        return $rows[0] ?? null;
    }

    public function firstOrFail($columns = ['*'])
    {
        $row = $this->first($columns);

        if (!$row) {
            throw new \RuntimeException('Record not found');
        }

        return $row;
    }

    public function exists(): bool
    {
        $query = clone $this;

        $query->state->select = ['1'];
        $query->state->skipNonAggregate = true;
        $query->state->distinct = false;
        $query->limit(1);

        $sql = $this->compiler->compileExists($query->state);
        $result = $this->executor->exists($sql, $query->state->bindings);

        return $result;
    }

    public function value(string $column)
    {
        $row = $this->first([$column]);

        if (!$row) {
            return null;
        }

        return is_array($row)
            ? ($row[$column] ?? null)
            : ($row->$column ?? null);
    }

    public function pluck(string $column): array
    {
        $rows = $this->get();

        return array_map(
            fn ($row) =>
                is_array($row)
                    ? ($row[$column] ?? null)
                    : ($row->$column ?? null),
            $rows
        );
    }

    public function sole($columns = ['*'])
    {
        $query = clone $this;

        if ($columns) {
            $query->select($columns);
        }

        $query->limit(2);
        $rows = $query->get();

        return $rows[0];
    }

    /* ======================================================
     | DML
     ====================================================== */

    public function insert(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = [];

        foreach ($data as $value) {
            $placeholders[] = $this->state->addBinding($value);
        }

        $sql = $this->compiler->compileInsert(
            $this->state,
            $columns,
            $placeholders
        );

        $result = $this->executor->insert($sql, $this->state->bindings);

        $this->state->reset();
        return $result;
    }

    public function update(array $data): int
    {
        if (!$this->state->where) {
            throw new \LogicException('Update without WHERE is not allowed');
        }

        $bindings = [];

        foreach ($data as $column => $value) {
            $bindings[$column] = $this->state->addBinding($value);
        }

        $sql = $this->compiler->compileUpdate(
            $this->state,
            $bindings
        );

        $affected = $this->executor->update(
            $sql,
            $this->state->bindings
        );

        $this->state->reset();

        return $affected;
    }

    public function delete(): int
    {
        if (!$this->state->where) {
            throw new \LogicException('Delete without WHERE is not allowed');
        }

        $sql = $this->compiler->compileDelete($this->state);

        $affected = $this->executor->delete(
            $sql,
            $this->state->bindings
        );

        $this->state->reset();

        return $affected;
    }

    /* ======================================================
     | Debug
     ====================================================== */

    public function toSql(): string
    {
        return $this->compiler->compileSelect($this->state);
    }

    public function tap(callable $callback)
    {
        $callback($this);
        return $this;
    }

    public function ddSql(): void
    {
        echo $this->toSql();
        exit;
    }
}
