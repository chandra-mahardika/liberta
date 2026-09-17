<?php

namespace Modules\Purchasing\Repositories;

use Liberta\Sql\DB;

class PurchaseInvoiceRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('purchase_invoices')
            ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_invoices.purchase_order_id');

        if (!empty($filters['status'])) {
            $query->where('purchase_invoices.status', '=', $filters['status']);
        }

        if (!empty($filters['purchase_order_id'])) {
            $query->where('purchase_invoices.purchase_order_id', '=', $filters['purchase_order_id']);
        }

        return $query->orderBy('purchase_invoices.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('purchase_invoices')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('purchase_invoices')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
