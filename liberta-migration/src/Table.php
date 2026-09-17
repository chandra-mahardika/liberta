<?php

namespace Liberta\Migration;

class Table
{
    private string $tableName = '';
    private array $columns = [];
    private ?string $primaryColumn = null;
    private array $indexes = [];
    private array $foreignKeys = [];

    public function __construct(string $name = '')
    {
        $this->tableName = $name;
    }

    public function id(string $name = 'id'): static
    {
        $this->primaryColumn = $name;
        $this->columns[$name] = 'INT AUTO_INCREMENT PRIMARY KEY';
        return $this;
    }

    public function uuid(string $name = 'id'): static
    {
        $this->primaryColumn = $name;
        $this->columns[$name] = 'VARCHAR(36) PRIMARY KEY';
        return $this;
    }

    public function string(string $name, int $length = 255): ColumnBuilder
    {
        return $this->addColumn($name, "VARCHAR({$length})");
    }

    public function text(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'TEXT');
    }

    public function integer(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'INT');
    }

    public function bigInteger(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'BIGINT');
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): ColumnBuilder
    {
        return $this->addColumn($name, "DECIMAL({$precision},{$scale})");
    }

    public function boolean(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'TINYINT(1)');
    }

    public function timestamp(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'TIMESTAMP');
    }

    public function datetime(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'DATETIME');
    }

    public function date(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'DATE');
    }

    public function json(string $name): ColumnBuilder
    {
        return $this->addColumn($name, 'JSON');
    }

    public function enum(string $name, array $values): ColumnBuilder
    {
        $list = implode(', ', array_map(fn ($v) => "'{$v}'", $values));
        return $this->addColumn($name, "ENUM({$list})");
    }

    public function timestamps(): void
    {
        $this->timestamp('created_at')->default('CURRENT_TIMESTAMP');
        $this->timestamp('updated_at')->default('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    public function softDeletes(): void
    {
        $this->timestamp('deleted_at')->nullable();
    }

    public function index(string ...$columns): void
    {
        $this->indexes[] = 'INDEX (`' . implode('`, `', $columns) . '`)';
    }

    public function unique(string ...$columns): void
    {
        $this->indexes[] = 'UNIQUE INDEX (`' . implode('`, `', $columns) . '`)';
    }

    public function foreign(string $column): ForeignKeyBuilder
    {
        return new ForeignKeyBuilder($column, $this);
    }

    public function toCreateSql(): string
    {
        $parts = [];

        foreach ($this->columns as $name => $definition) {
            $parts[] = "`{$name}` {$definition}";
        }

        foreach ($this->indexes as $index) {
            $parts[] = $index;
        }

        $body = implode(",\n    ", $parts);

        return "CREATE TABLE IF NOT EXISTS `{$this->getName()}` (\n    {$body}\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    }

    private function getName(): string
    {
        // Table name is set by Schema::create() — stored via reference
        return $this->tableName ?? 'unknown';
    }

    private function addColumn(string $name, string $type): ColumnBuilder
    {
        $builder = new ColumnBuilder($name, $type);
        $this->columns[$name] = &$builder;
        return $builder;
    }
}
