<?php

namespace Liberta\Sql\Executor;

interface Executor
{
    public function select(string $sql, array $bindings): array;

    public function exists(string $sql, array $bindings): bool;

    public function insert(string $sql, array $bindings): bool;

    public function update(string $sql, array $bindings): int;

    public function delete(string $sql, array $bindings): int;
}

