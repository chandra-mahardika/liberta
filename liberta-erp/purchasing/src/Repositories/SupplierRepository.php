<?php

namespace Modules\Purchasing\Repositories;

use Liberta\Sql\DB;

class SupplierRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('suppliers');

        if (!empty($filters['search'])) {
            $query->like('name', $filters['search']);
        }

        return $query->orderBy('name', 'ASC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('suppliers')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('suppliers')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('suppliers')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('suppliers')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
