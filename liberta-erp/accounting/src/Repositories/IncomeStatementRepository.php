<?php

namespace Modules\Accounting\Repositories;

use Liberta\Sql\DB;

class IncomeStatementRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function getRevenue(array $params = []): array
    {
        $query = $this->db->table('journal_lines')
            ->select('chart_of_accounts.name')
            ->selectRaw('COALESCE(SUM(journal_lines.credit - journal_lines.debit), 0) AS total')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->where('chart_of_accounts.type', '=', 'revenue')
            ->where('journal_entries.status', '=', 'posted')
            ->groupBy('chart_of_accounts.id');

        if (!empty($params['date_from'])) {
            $query->where('journal_entries.date', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->where('journal_entries.date', '<=', $params['date_to']);
        }

        $items = $query->orderBy('chart_of_accounts.code', 'ASC')->get();
        $total = array_sum(array_column($items, 'total'));

        return ['items' => $items, 'total' => $total];
    }

    public function getExpenses(array $params = []): array
    {
        $query = $this->db->table('journal_lines')
            ->select('chart_of_accounts.name')
            ->selectRaw('COALESCE(SUM(journal_lines.debit - journal_lines.credit), 0) AS total')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->where('chart_of_accounts.type', '=', 'expense')
            ->where('journal_entries.status', '=', 'posted')
            ->groupBy('chart_of_accounts.id');

        if (!empty($params['date_from'])) {
            $query->where('journal_entries.date', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->where('journal_entries.date', '<=', $params['date_to']);
        }

        $items = $query->orderBy('chart_of_accounts.code', 'ASC')->get();
        $total = array_sum(array_column($items, 'total'));

        return ['items' => $items, 'total' => $total];
    }
}
