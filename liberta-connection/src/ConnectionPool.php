<?php

declare(strict_types=1);

namespace Liberta\Connection;

class ConnectionPool
{
    /** @var \PDO[] */
    private array $connections = [];
    private array $config = [];
    private int $maxConnections;
    private int $minConnections;
    private int $lifetime;
    private array $lastUsed = [];

    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        array $options = [],
        int $minConnections = 1,
        int $maxConnections = 10,
        int $lifetime = 3600
    ) {
        $this->config = compact('dsn', 'username', 'password', 'options');
        $this->minConnections = $minConnections;
        $this->maxConnections = $maxConnections;
        $this->lifetime = $lifetime;

        $this->warmUp();
    }

    public function getConnection(): \PDO
    {
        $this->cleanStaleConnections();

        if (!empty($this->connections)) {
            $key = array_key_first($this->connections);
            $this->lastUsed[$key] = time();
            return $this->connections[$key];
        }

        if (count($this->connections) < $this->maxConnections) {
            return $this->createConnection();
        }

        throw new \RuntimeException("Connection pool exhausted (max: {$this->maxConnections})");
    }

    public function release(\PDO $connection): void
    {
        // Connection stays in pool for reuse
    }

    public function closeAll(): void
    {
        $this->connections = [];
        $this->lastUsed = [];
    }

    public function getConnectionCount(): int
    {
        return count($this->connections);
    }

    public function getAvailableCount(): int
    {
        return $this->maxConnections - count($this->connections);
    }

    private function warmUp(): void
    {
        for ($i = 0; $i < $this->minConnections; $i++) {
            $this->createConnection();
        }
    }

    private function createConnection(): \PDO
    {
        $pdo = new \PDO(
            $this->config['dsn'],
            $this->config['username'],
            $this->config['password'],
            $this->config['options']
        );

        $key = spl_object_id($pdo);
        $this->connections[$key] = $pdo;
        $this->lastUsed[$key] = time();

        return $pdo;
    }

    private function cleanStaleConnections(): void
    {
        $now = time();

        foreach ($this->connections as $key => $pdo) {
            if (($now - ($this->lastUsed[$key] ?? 0)) > $this->lifetime) {
                unset($this->connections[$key], $this->lastUsed[$key]);
            }
        }
    }
}
