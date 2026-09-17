<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Repositories\BalanceSheetRepository;

class BalanceSheetService
{
    public function __construct(
        protected BalanceSheetRepository $repo
    ) {}

    public function generate(array $params = []): array
    {
        $assets = $this->repo->getAssets($params);
        $liabilities = $this->repo->getLiabilities($params);
        $equity = $this->repo->getEquity($params);

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_liabilities_and_equity' => $liabilities['total'] + $equity['total'],
        ];
    }
}
