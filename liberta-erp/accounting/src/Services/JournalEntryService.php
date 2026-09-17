<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Repositories\JournalEntryRepository;

class JournalEntryService
{
    public function __construct(
        protected JournalEntryRepository $repo
    ) {}

    public function all(array $filters = []): array
    {
        return $this->repo->all($filters);
    }

    public function find(int|string $id): ?array
    {
        return $this->repo->findWithLines($id);
    }

    public function create(array $data): array
    {
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $this->repo->create($data);
        $entryId = $this->repo->lastInsertId();

        foreach ($lines as $line) {
            $this->repo->addLine($entryId, $line);
        }

        return $this->repo->findWithLines($entryId);
    }

    public function update(int|string $id, array $data): array
    {
        $this->repo->update($id, $data);
        return $this->repo->findWithLines($id);
    }

    public function delete(int|string $id): bool
    {
        $this->repo->deleteLines($id);
        return $this->repo->delete($id);
    }
}
