<?php

namespace Liberta\Cache;

class FileCache implements CacheStore
{
    private string $path;

    public function __construct(string $path = '/tmp/liberta_cache')
    {
        $this->path = rtrim($path, '/');

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->filePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $data = [
            'value' => $value,
            'expires_at' => $ttl > 0 ? time() + $ttl : null,
        ];

        return file_put_contents(
            $this->filePath($key),
            serialize($data),
            LOCK_EX
        ) !== false;
    }

    public function delete(string $key): bool
    {
        $file = $this->filePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

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
        $files = glob($this->path . '/*.cache');

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }

    public function tags(array $tags): TaggedCache
    {
        return new ArrayTaggedCache(new ArrayCache(), $tags);
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

    private function filePath(string $key): string
    {
        return $this->path . '/' . md5($key) . '.cache';
    }
}
