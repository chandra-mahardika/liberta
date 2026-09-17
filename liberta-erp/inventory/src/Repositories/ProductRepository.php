<?php

namespace Modules\Inventory\Repositories;

use Liberta\Sql\DB;

class ProductRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id', 'LEFT');

        if (!empty($filters['category_id'])) {
            $query->where('products.category_id', '=', $filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $query->like('products.name', $filters['search']);
        }

        if (!empty($filters['sku'])) {
            $query->where('products.sku', '=', $filters['sku']);
        }

        return $query->orderBy('products.name', 'ASC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('products')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('products')->insert($data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->table('products')
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(int|string $id): int
    {
        return $this->db->table('products')
            ->where('id', '=', $id)
            ->delete();
    }

    public function getStock(int|string $productId, int|string $warehouseId): float
    {
        $result = $this->db->table('stock_movements')
            ->selectRaw('COALESCE(SUM(CASE WHEN type IN ("in","adjustment") THEN quantity WHEN type = "out" THEN -quantity ELSE 0 END), 0) AS stock')
            ->where('product_id', '=', $productId)
            ->where('warehouse_id', '=', $warehouseId)
            ->first();

        return (float) ($result['stock'] ?? 0);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
