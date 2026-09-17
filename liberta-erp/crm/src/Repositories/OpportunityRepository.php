<?php

namespace Modules\Crm\Repositories;

use Liberta\Sql\DB;

class OpportunityRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('opportunities')
            ->join('crm_contacts', 'crm_contacts.id', '=', 'opportunities.contact_id');

        if (!empty($filters['stage'])) {
            $query->where('opportunities.stage', '=', $filters['stage']);
        }

        if (!empty($filters['contact_id'])) {
            $query->where('opportunities.contact_id', '=', $filters['contact_id']);
        }

        if (!empty($filters['lead_id'])) {
            $query->where('opportunities.lead_id', '=', $filters['lead_id']);
        }

        return $query->orderBy('opportunities.created_at', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('opportunities')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('opportunities')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('opportunities')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('opportunities')
            ->where('id', '=', $id)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
