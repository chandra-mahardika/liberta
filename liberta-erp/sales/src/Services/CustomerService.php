<?php

namespace Modules\Sales\Services;

use Modules\Sales\Repositories\CustomerRepository;

class CustomerService
{
    public function __construct(
        protected CustomerRepository $repo
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

    public function update(int|string $id, array $data): array
    {
        $this->repo->update($id, $data);
        return $this->repo->find($id);
    }

    public function delete(int|string $id): bool
    {
        return $this->repo->delete($id);
    }
}
