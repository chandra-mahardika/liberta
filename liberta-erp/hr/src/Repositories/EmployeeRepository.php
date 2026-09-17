<?php

namespace Modules\Hr\Repositories;

use Liberta\Sql\DB;

class EmployeeRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('employees')
            ->join('departments', 'departments.id', '=', 'employees.department_id', 'LEFT')
            ->join('positions', 'positions.id', '=', 'employees.position_id', 'LEFT');

        if (!empty($filters['department_id'])) {
            $query->where('employees.department_id', '=', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('employees.status', '=', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->like('employees.name', $filters['search']);
        }

        return $query->orderBy('employees.name', 'ASC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('employees')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('employees')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('employees')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('employees')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
