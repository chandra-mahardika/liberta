<?php

namespace Modules\Sales\Repositories;

use Liberta\Sql\DB;

class SalesOrderRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('sales_orders')
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id');

        if (!empty($filters['status'])) {
            $query->where('sales_orders.status', '=', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('sales_orders.customer_id', '=', $filters['customer_id']);
        }

        return $query->orderBy('sales_orders.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('sales_orders')
            ->where('id', '=', $id)
            ->first();
    }

    public function findWithItems(int|string $id): ?array
    {
        $order = $this->find($id);

        if ($order) {
            $order['items'] = $this->db->table('sales_order_items')
                ->join('products', 'products.id', '=', 'sales_order_items.product_id')
                ->where('sales_order_id', '=', $id)
                ->get();
        }

        return $order;
    }

    public function create(array $data): bool
    {
        return $this->db->table('sales_orders')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('sales_orders')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('sales_orders')
            ->where('id', '=', $id)
            ->delete();
    }

    public function addItem(int|string $orderId, array $data): bool
    {
        $data['sales_order_id'] = $orderId;
        return $this->db->table('sales_order_items')->insert($data);
    }

    public function deleteItems(int|string $orderId): int
    {
        return $this->db->table('sales_order_items')
            ->where('sales_order_id', '=', $orderId)
            ->delete();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
