<?php

namespace Modules\Crm\Repositories;

use Liberta\Sql\DB;

class NoteRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('notes');

        if (!empty($filters['contact_id'])) {
            $query->where('contact_id', '=', $filters['contact_id']);
        }

        if (!empty($filters['lead_id'])) {
            $query->where('lead_id', '=', $filters['lead_id']);
        }

        if (!empty($filters['opportunity_id'])) {
            $query->where('opportunity_id', '=', $filters['opportunity_id']);
        }

        if (!empty($filters['search'])) {
            $query->like('title', $filters['search']);
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('notes')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('notes')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('notes')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('notes')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
