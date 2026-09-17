<?php

namespace Liberta\Cache;

class CacheManager
{
    /** @var array<string, CacheStore> */
    private array $stores = [];
    private string $default;

    public function __construct(
        private array $config = [],
        string $default = 'array'
    ) {
        $this->default = $default;
    }

    public function store(?string $name = null): CacheStore
    {
        $name ??= $this->default;

        if (!isset($this->stores[$name])) {
            $this->stores[$name] = $this->resolve($name);
        }

        return $this->stores[$name];
    }

    public function default(): CacheStore
    {
        return $this->store();
    }

    private function resolve(string $name): CacheStore
    {
        $config = $this->config[$name] ?? ['driver' => 'array'];

        return match ($config['driver']) {
            'file' => new FileCache($config['path'] ?? '/tmp/liberta_cache'),
            default => new ArrayCache(),
        };
    }
}
