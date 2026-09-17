<?php

namespace Modules\Purchasing\Services;

use Modules\Purchasing\Repositories\PurchaseOrderRepository;

class PurchaseOrderService
{
    public function __construct(
        protected PurchaseOrderRepository $repo
    ) {}

    public function all(array $filters = []): array
    {
        return $this->repo->all($filters);
    }

    public function find(int|string $id): ?array
    {
        return $this->repo->findWithItems($id);
    }

    public function create(array $data): array
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $this->repo->create($data);
        $orderId = $this->repo->lastInsertId();

        foreach ($items as $item) {
            $this->repo->addItem($orderId, $item);
        }

        return $this->repo->findWithItems($orderId);
    }

    public function update(int|string $id, array $data): array
    {
        $this->repo->update($id, $data);
        return $this->repo->findWithItems($id);
    }

    public function delete(int|string $id): bool
    {
        $this->repo->deleteItems($id);
        return $this->repo->delete($id);
    }
}
