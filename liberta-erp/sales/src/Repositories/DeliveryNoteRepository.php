<?php

namespace Modules\Sales\Repositories;

use Liberta\Sql\DB;

class DeliveryNoteRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function all(array $filters = []): array
    {
        $query = $this->db->table('delivery_notes')
            ->join('sales_orders', 'sales_orders.id', '=', 'delivery_notes.sales_order_id');

        if (!empty($filters['sales_order_id'])) {
            $query->where('delivery_notes.sales_order_id', '=', $filters['sales_order_id']);
        }

        return $query->orderBy('delivery_notes.date', 'DESC')->get();
    }

    public function find(int|string $id): ?array
    {
        return $this->db->table('delivery_notes')
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('delivery_notes')->insert($data);
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->pdo()->lastInsertId();
    }
}
