<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Repositories\IncomeStatementRepository;

class IncomeStatementService
{
    public function __construct(
        protected IncomeStatementRepository $repo
    ) {}

    public function generate(array $params = []): array
    {
        $revenue = $this->repo->getRevenue($params);
        $expenses = $this->repo->getExpenses($params);

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_income' => $revenue['total'] - $expenses['total'],
        ];
    }
}
