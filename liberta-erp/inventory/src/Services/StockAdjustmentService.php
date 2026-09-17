<?php

namespace Modules\Inventory\Services;

use Modules\Inventory\Repositories\StockAdjustmentRepository;

class StockAdjustmentService
{
    public function __construct(
        protected StockAdjustmentRepository $repo
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
        $this->repo->create($data);
        return $this->repo->find($this->repo->lastInsertId());
    }
}
