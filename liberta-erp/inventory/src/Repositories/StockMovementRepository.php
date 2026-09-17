<?php

namespace Modules\Inventory\Repositories;

use Liberta\Sql\DB;

class StockMovementRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('stock_movements')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'stock_movements.warehouse_id');

        if (!empty($filters['product_id'])) {
            $query->where('stock_movements.product_id', '=', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('stock_movements.warehouse_id', '=', $filters['warehouse_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('stock_movements.type', '=', $filters['type']);
        }

        return $query->orderBy('stock_movements.created_at', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('stock_movements')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('stock_movements')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
