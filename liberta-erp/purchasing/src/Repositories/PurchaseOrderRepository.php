<?php

namespace Modules\Purchasing\Repositories;

use Liberta\Sql\DB;

class PurchaseOrderRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('purchase_orders')
            ->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id');

        if (!empty($filters['status'])) {
            $query->where('purchase_orders.status', '=', $filters['status']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('purchase_orders.supplier_id', '=', $filters['supplier_id']);
        }

        return $query->orderBy('purchase_orders.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('purchase_orders')
            ->where('id', '=', $id)
            ->first();
    }

    public function findWithItems(int|string $id): ?array
    {
        $order = $this->find($id);

        if ($order) {
            $order['items'] = $this->db->table('purchase_order_items')
                ->join('products', 'products.id', '=', 'purchase_order_items.product_id')
                ->where('purchase_order_id', '=', $id)
                ->get();
        }

        return $order;
    }

    public function create(array $data): bool
    {
        return $this->db->table('purchase_orders')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('purchase_orders')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('purchase_orders')
            ->where('id', '=', $id)
            ->delete();
    }

    public function addItem(int|string $orderId, array $data): bool
    {
        $data['purchase_order_id'] = $orderId;
        return $this->db->table('purchase_order_items')->insert($data);
    }

    public function deleteItems(int|string $orderId): int
    {
        return $this->db->table('purchase_order_items')
            ->where('purchase_order_id', '=', $orderId)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
