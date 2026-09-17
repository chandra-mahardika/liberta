<?php

declare(strict_types=1);

namespace Liberta\SqlBuilder\Cache;

use Liberta\Cache\CacheStore;

class QueryCache
{
    public function __construct(
        private CacheStore $cache,
        private int $defaultTtl = 60,
        private string $prefix = 'query:'
    ) {}

    public function get(string $key): ?array
    {
        $cached = $this->cache->get($this->prefix . $key);

        if ($cached === null) {
            return null;
        }

        $data = json_decode($cached, true);

        if ($data === null) {
            return null;
        }

        if (isset($data['expires_at']) && $data['expires_at'] < time()) {
            $this->cache->delete($this->prefix . $key);
            return null;
        }

        return $data['result'];
    }

    public function set(string $key, array $result, int $ttl = 0): void
    {
        $ttl = $ttl > 0 ? $ttl : $this->defaultTtl;

        $data = json_encode([
            'result' => $result,
            'expires_at' => time() + $ttl,
            'cached_at' => time(),
        ]);

        $this->cache->set($this->prefix . $key, $data, $ttl);
    }

    public function invalidate(string $pattern): int
    {
        $count = 0;
        $keys = $this->cache->getMultiple(array_keys($_ENV ?? []));

        foreach ($keys as $key => $value) {
            if (str_starts_with($key, $this->prefix)) {
                $this->cache->delete($key);
                $count++;
            }
        }

        return $count;
    }

    public function flush(): void
    {
        $this->cache->flush();
    }

    public function generateKey(string $sql, array $bindings): string
    {
        return md5($sql . serialize($bindings));
    }
}
