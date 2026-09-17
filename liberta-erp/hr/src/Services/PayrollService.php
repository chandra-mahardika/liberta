<?php

namespace Modules\Hr\Services;

use Modules\Hr\Repositories\PayrollRepository;

class PayrollService
{
    public function __construct(
        protected PayrollRepository $repo
    ) {}

    public function all(array $filters = []): array
    {
        return $this->repo->all($filters);
    }

    public function find(int|string $id): ?array
    {
        return $this->repo->find($id);
    }

    public function create(array $data): array
    {
        $data['net_salary'] = ($data['base_salary'] ?? 0)
            + ($data['allowances'] ?? 0)
            - ($data['deductions'] ?? 0);

        $this->repo->create($data);
        return $this->repo->find($this->repo->lastInsertId());
    }
}
