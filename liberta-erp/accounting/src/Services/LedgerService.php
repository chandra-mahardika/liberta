<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Repositories\LedgerRepository;

class LedgerService
{
    public function __construct(
        protected LedgerRepository $repo
    ) {}

    public function all(array $filters = []): array
    {
        return $this->repo->all($filters);
    }

    public function find(int|string $id): ?array
    {
        return $this->repo->find($id);
    }

    public function byAccount(int|string $accountId, array $filters = []): array
    {
        return $this->repo->byAccount($accountId, $filters);
    }
}
