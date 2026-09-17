<?php

namespace Modules\Accounting\Repositories;

use Liberta\Sql\DB;

class LedgerRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_lines.account_id');

        if (!empty($filters['account_id'])) {
            $query->where('journal_lines.account_id', '=', $filters['account_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('journal_entries.date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('journal_entries.date', '<=', $filters['date_to']);
        }

        return $query->orderBy('journal_entries.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_lines.id', '=', $id)
            ->first();
    }

    public function byAccount(int|string $accountId, array $filters = []): array
    {
        $query = $this->db->table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_lines.account_id', '=', $accountId);

        return $query->orderBy('journal_entries.date', 'ASC')->get();
    }
}
