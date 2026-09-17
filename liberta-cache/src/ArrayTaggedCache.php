<?php

namespace Liberta\Cache;

class ArrayTaggedCache implements TaggedCache
{
    public function __construct(
        private ArrayCache $cache,
        private array $tags
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($this->taggedKey($key), $default);
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->cache->set($this->taggedKey($key), $value, $ttl);
    }

    public function delete(string $key): bool
    {
        return $this->cache->delete($this->taggedKey($key));
    }

    public function flush(): bool
    {
        // In a real implementation, this would flush all keys with matching tags
        return true;
    }

    private function taggedKey(string $key): string
    {
        sort($this->tags);
        return implode(':', $this->tags) . ':' . $key;
    }
}
