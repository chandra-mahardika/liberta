<?php

namespace Modules\Accounting\Repositories;

use Liberta\Sql\DB;

class JournalEntryRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('journal_entries');

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('journal_entries')
            ->where('id', '=', $id)
            ->first();
    }

    public function findWithLines(int|string $id): ?array
    {
        $entry = $this->find($id);

        if ($entry) {
            $entry['lines'] = $this->db->table('journal_lines')
                ->where('journal_entry_id', '=', $id)
                ->get();
        }

        return $entry;
    }

    public function create(array $data): bool
    {
        return $this->db->table('journal_entries')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('journal_entries')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('journal_entries')
            ->where('id', '=', $id)
            ->delete();
    }

    public function addLine(int|string $entryId, array $data): bool
    {
        $data['journal_entry_id'] = $entryId;
        return $this->db->table('journal_lines')->insert($data);
    }

    public function deleteLines(int|string $entryId): int
    {
        return $this->db->table('journal_lines')
            ->where('journal_entry_id', '=', $entryId)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
