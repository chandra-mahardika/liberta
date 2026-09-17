<?php

namespace Modules\Hr\Repositories;

use Liberta\Sql\DB;

class PayrollRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('payroll')
            ->join('employees', 'employees.id', '=', 'payroll.employee_id');

        if (!empty($filters['employee_id'])) {
            $query->where('payroll.employee_id', '=', $filters['employee_id']);
        }

        if (!empty($filters['period'])) {
            $query->where('payroll.period', '=', $filters['period']);
        }

        if (!empty($filters['status'])) {
            $query->where('payroll.status', '=', $filters['status']);
        }

        return $query->orderBy('payroll.period', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('payroll')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('payroll')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
