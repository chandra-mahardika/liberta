<?php

namespace Modules\Purchasing\Repositories;

use Liberta\Sql\DB;

class PurchaseReceiptRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('purchase_receipts')
            ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_receipts.purchase_order_id');

        if (!empty($filters['purchase_order_id'])) {
            $query->where('purchase_receipts.purchase_order_id', '=', $filters['purchase_order_id']);
        }

        return $query->orderBy('purchase_receipts.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('purchase_receipts')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('purchase_receipts')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
