<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Repositories\TrialBalanceRepository;

class TrialBalanceService
{
    public function __construct(
        protected TrialBalanceRepository $repo
    ) {}

    public function generate(array $params = []): array
    {
        $accounts = $this->repo->getAccountBalances($params);
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as &$account) {
            $totalDebit += $account['debit'];
            $totalCredit += $account['credit'];
        }

        return [
            'accounts' => $accounts,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }
}
