<?php

namespace Modules\Sales\Repositories;

use Liberta\Sql\DB;

class SalesInvoiceRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('sales_invoices')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_invoices.sales_order_id');

        if (!empty($filters['status'])) {
            $query->where('sales_invoices.status', '=', $filters['status']);
        }

        if (!empty($filters['sales_order_id'])) {
            $query->where('sales_invoices.sales_order_id', '=', $filters['sales_order_id']);
        }

        return $query->orderBy('sales_invoices.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('sales_invoices')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('sales_invoices')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
