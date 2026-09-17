<?php

namespace Liberta\Connection;

use Liberta\Sql\DB;

class ConnectionManager
{
    /** @var array<string, array> Raw config per connection name */
    private array $configs = [];

    /** @var array<string, DB> Resolved DB instances (lazy) */
    private array $instances = [];

    /** @var string Default connection name */
    private string $default;

    /** @var string Active connection name */
    private string $active;

    /**
     * @param array<string, array> $configs  Map of name => DB config
     * @param string               $default  Default connection name
     */
    public function __construct(array $configs, string $default = 'primary')
    {
        $this->configs = $configs;
        $this->default = $default;
        $this->active = $default;

        if (!isset($this->configs[$default])) {
            throw new \InvalidArgumentException(
                "Default connection [{$default}] not found in config"
            );
        }
    }

    /**
     * Get a DB instance by connection name.
     * Connections are lazily created and cached.
     */
    public function connection(?string $name = null): DB
    {
        $name ??= $this->active;

        if (!isset($this->configs[$name])) {
            throw new \InvalidArgumentException(
                "Connection [{$name}] not found in config"
            );
        }

        if (!isset($this->instances[$name])) {
            $this->instances[$name] = new DB($this->configs[$name]);
        }

        return $this->instances[$name];
    }

    /**
     * Get the default connection.
     */
    public function default(): DB
    {
        return $this->connection($this->default);
    }

    /**
     * Set the active connection name (used by TenantContext).
     */
    public function setActive(string $name): void
    {
        if (!isset($this->configs[$name])) {
            throw new \InvalidArgumentException(
                "Connection [{$name}] not found in config"
            );
        }

        $this->active = $name;
    }

    /**
     * Get the currently active connection name.
     */
    public function getActive(): string
    {
        return $this->active;
    }

    /**
     * Get all registered connection names.
     */
    public function names(): array
    {
        return array_keys($this->configs);
    }

    /**
     * Check if a connection name is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->configs[$name]);
    }

    /**
     * Forget (close) a cached connection instance.
     */
    public function forget(string $name): void
    {
        unset($this->instances[$name]);
    }

    /**
     * Forget all cached connection instances.
     */
    public function forgetAll(): void
    {
        $this->instances = [];
    }
}
