<?php

namespace Modules\Crm\Repositories;

use Liberta\Sql\DB;

class LeadRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('leads')
            ->join('crm_contacts', 'crm_contacts.id', '=', 'leads.contact_id');

        if (!empty($filters['status'])) {
            $query->where('leads.status', '=', $filters['status']);
        }

        if (!empty($filters['source'])) {
            $query->where('leads.source', '=', $filters['source']);
        }

        if (!empty($filters['contact_id'])) {
            $query->where('leads.contact_id', '=', $filters['contact_id']);
        }

        return $query->orderBy('leads.created_at', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('leads')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('leads')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('leads')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('leads')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
