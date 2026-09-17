<?php

namespace Modules\Hr\Repositories;

use Liberta\Sql\DB;

class AttendanceRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('attendance')
            ->join('employees', 'employees.id', '=', 'attendance.employee_id');

        if (!empty($filters['employee_id'])) {
            $query->where('attendance.employee_id', '=', $filters['employee_id']);
        }

        if (!empty($filters['date'])) {
            $query->where('attendance.date', '=', $filters['date']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('attendance.date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('attendance.date', '<=', $filters['date_to']);
        }

        return $query->orderBy('attendance.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('attendance')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('attendance')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('attendance')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
