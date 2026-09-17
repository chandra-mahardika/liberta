<?php

namespace Liberta\Sql\Executor;

use PDO;

final class PdoExecutor implements Executor
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function select(string $sql, array $bindings): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function exists(string $sql, array $bindings): bool
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return (bool) $stmt->fetchColumn();
    }

    public function insert(string $sql, array $bindings): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($bindings);
    }

    public function update(string $sql, array $bindings): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public function delete(string $sql, array $bindings): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }
}
