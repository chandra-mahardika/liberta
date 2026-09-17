<?php

namespace Modules\Accounting\Repositories;

use Liberta\Sql\DB;

class ChartOfAccountsRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('chart_of_accounts');

        if (!empty($filters['type'])) {
            $query->where('type', '=', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->like('name', $filters['search']);
        }

        return $query->orderBy('code', 'ASC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('chart_of_accounts')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('chart_of_accounts')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('chart_of_accounts')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('chart_of_accounts')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
