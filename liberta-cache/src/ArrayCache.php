<?php

namespace Liberta\Cache;

class ArrayCache implements CacheStore
{
    private array $storage = [];
    private array $tags = [];

    public function get(string $key, mixed $default = null): mixed
    {
        $item = $this->storage[$key] ?? null;

        if ($item === null) {
            return $default;
        }

        if (isset($item['expires_at']) && $item['expires_at'] < time()) {
            unset($this->storage[$key]);
            return $default;
        }

        return $item['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $this->storage[$key] = [
            'value' => $value,
            'expires_at' => $ttl > 0 ? time() + $ttl : null,
        ];

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->storage[$key]);
        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        return $value;
    }

    public function forget(string $key): bool
    {
        return $this->delete($key);
    }

    public function flush(): bool
    {
        $this->storage = [];
        $this->tags = [];
        return true;
    }

    public function increment(string $key, int $value = 1): int
    {
        $current = $this->get($key, 0);
        $new = (int) $current + $value;
        $this->set($key, $new);
        return $new;
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }

    public function tags(array $tags): TaggedCache
    {
        return new ArrayTaggedCache($this, $tags);
    }

    public function getMultiple(array $keys, mixed $default = null): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }
}
