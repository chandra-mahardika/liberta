<?php

namespace Liberta\Migration;

class ColumnBuilder
{
    private string $definition;
    private bool $nullable = false;
    private ?string $default = null;

    public function __construct(
        private string $name,
        private string $type
    ) {
        $this->definition = $type;
    }

    public function nullable(): static
    {
        $this->nullable = true;
        return $this;
    }

    public function default(mixed $value): static
    {
        if (is_string($value)) {
            $this->default = "'{$value}'";
        } elseif (is_int($value) || is_float($value)) {
            $this->default = (string) $value;
        } elseif ($value === null) {
            $this->default = 'NULL';
        } else {
            $this->default = "'{$value}'";
        }
        return $this;
    }

    public function unique(): static
    {
        $this->definition .= ' UNIQUE';
        return $this;
    }

    public function unsigned(): static
    {
        $this->definition .= ' UNSIGNED';
        return $this;
    }

    public function autoIncrement(): static
    {
        $this->definition .= ' AUTO_INCREMENT';
        return $this;
    }

    public function __toString(): string
    {
        $sql = $this->definition;

        if ($this->nullable) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if ($this->default !== null) {
            $sql .= " DEFAULT {$this->default}";
        }

        return $sql;
    }
}
