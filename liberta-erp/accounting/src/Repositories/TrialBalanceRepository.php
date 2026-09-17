<?php

namespace Modules\Accounting\Repositories;

use Liberta\Sql\DB;

class TrialBalanceRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function getAccountBalances(array $params = []): array
    {
        $query = $this->db->table('chart_of_accounts')
            ->select('chart_of_accounts.*')
            ->selectRaw('COALESCE(SUM(journal_lines.debit), 0) AS debit')
            ->selectRaw('COALESCE(SUM(journal_lines.credit), 0) AS credit')
            ->join('journal_lines', 'journal_lines.account_id', '=', 'chart_of_accounts.id')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.status', '=', 'posted')
            ->groupBy('chart_of_accounts.id');

        if (!empty($params['date_from'])) {
            $query->where('journal_entries.date', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->where('journal_entries.date', '<=', $params['date_to']);
        }

        return $query->orderBy('chart_of_accounts.code', 'ASC')->get();
    }
}
