<?php

namespace Modules\Inventory\Repositories;

use Liberta\Sql\DB;

class WarehouseRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        return $this->db->table('warehouses')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('warehouses')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('warehouses')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('warehouses')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('warehouses')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
