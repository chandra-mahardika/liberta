<?php

declare(strict_types=1);

namespace Liberta\SqlBuilder\Cache;

use Liberta\Cache\CacheStore;
use Liberta\SqlBuilder\DB;
use Liberta\SqlBuilder\Executor\Executor;

class CachedExecutor
{
    private QueryCache $cache;

    public function __construct(
        private Executor $executor,
        CacheStore $cacheStore,
        int $defaultTtl = 60
    ) {
        $this->cache = new QueryCache($cacheStore, $defaultTtl);
    }

    public function execute(string $sql, array $bindings = []): array
    {
        $key = $this->cache->generateKey($sql, $bindings);

        $cached = $this->cache->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $result = $this->executor->execute($sql, $bindings);

        if ($this->isReadQuery($sql)) {
            $this->cache->set($key, $result);
        }

        return $result;
    }

    public function invalidateTable(string $table): void
    {
        $this->cache->invalidate($table);
    }

    public function flush(): void
    {
        $this->cache->flush();
    }

    private function isReadQuery(string $sql): bool
    {
        $trimmed = ltrim($sql);
        $upper = strtoupper($trimmed);

        return str_starts_with($upper, 'SELECT')
            || str_starts_with($upper, 'SHOW')
            || str_starts_with($upper, 'DESCRIBE')
            || str_starts_with($upper, 'EXPLAIN');
    }
}
