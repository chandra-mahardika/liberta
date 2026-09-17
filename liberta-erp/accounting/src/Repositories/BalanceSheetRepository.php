<?php

namespace Modules\Accounting\Repositories;

use Liberta\Sql\DB;

class BalanceSheetRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function getAssets(array $params = []): array
    {
        $items = $this->getByType('asset', $params);
        return ['items' => $items, 'total' => array_sum(array_column($items, 'balance'))];
    }

    public function getLiabilities(array $params = []): array
    {
        $items = $this->getByType('liability', $params);
        return ['items' => $items, 'total' => array_sum(array_column($items, 'balance'))];
    }

    public function getEquity(array $params = []): array
    {
        $items = $this->getByType('equity', $params);
        return ['items' => $items, 'total' => array_sum(array_column($items, 'balance'))];
    }

    private function getByType(string $type, array $params = []): array
    {
        $query = $this->db->table('journal_lines')
            ->select('chart_of_accounts.*')
            ->selectRaw('COALESCE(SUM(journal_lines.debit - journal_lines.credit), 0) AS balance')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->where('chart_of_accounts.type', '=', $type)
            ->where('journal_entries.status', '=', 'posted')
            ->groupBy('chart_of_accounts.id');

        if (!empty($params['as_of'])) {
            $query->where('journal_entries.date', '<=', $params['as_of']);
        }

        return $query->orderBy('chart_of_accounts.code', 'ASC')->get();
    }
}
