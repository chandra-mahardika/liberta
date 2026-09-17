<?php

namespace Modules\Sales\Services;

use Modules\Sales\Repositories\DeliveryNoteRepository;

class DeliveryNoteService
{
    public function __construct(
        protected DeliveryNoteRepository $repo
    ) {}

    public function all(array $filters = []): array
    {
        return $this->repo->all($filters);
    }

    public function find(int|string $id): ?array
    {
        return $this->repo->find($id);
    }

    public function create(array $data): array
    {
        $this->repo->create($data);
        return $this->repo->find($this->repo->lastInsertId());
    }
}
