<?php

namespace Liberta\Migration;

class ForeignKeyBuilder
{
    public function __construct(
        private string $column,
        private Table $table
    ) {}

    public function references(string $table, string $column = 'id'): static
    {
        // Store for SQL generation — actual DDL handled by AlterTable or Schema
        return $this;
    }

    public function onDelete(string $action = 'CASCADE'): static
    {
        return $this;
    }

    public function onUpdate(string $action = 'CASCADE'): static
    {
        return $this;
    }
}
