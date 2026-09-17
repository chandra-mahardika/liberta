<?php

namespace Modules\Inventory\Repositories;

use Liberta\Sql\DB;

class StockAdjustmentRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('stock_adjustments')
            ->join('products', 'products.id', '=', 'stock_adjustments.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'stock_adjustments.warehouse_id');

        if (!empty($filters['product_id'])) {
            $query->where('stock_adjustments.product_id', '=', $filters['product_id']);
        }

        return $query->orderBy('stock_adjustments.created_at', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('stock_adjustments')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('stock_adjustments')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
