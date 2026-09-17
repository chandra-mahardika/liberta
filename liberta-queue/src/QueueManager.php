<?php

namespace Liberta\Queue;

class QueueManager
{
    /** @var array<string, QueueDriver> */
    private array $drivers = [];
    private string $default;

    public function __construct(
        private array $config = [],
        string $default = 'sync'
    ) {
        $this->default = $default;
    }

    public function driver(?string $name = null): QueueDriver
    {
        $name ??= $this->default;

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->resolve($name);
        }

        return $this->drivers[$name];
    }

    public function push(Job $job, string $queue = 'default'): string
    {
        return $this->driver()->push($job, $queue);
    }

    public function later(int $delay, Job $job, string $queue = 'default'): string
    {
        return $this->driver()->later($delay, $job, $queue);
    }

    public function laterOn(string $queue, int $delay, Job $job): string
    {
        return $this->driver()->later($delay, $job, $queue);
    }

    private function resolve(string $name): QueueDriver
    {
        $config = $this->config[$name] ?? ['driver' => 'sync'];

        return match ($config['driver']) {
            'sync' => new Drivers\SyncQueueDriver(),
            'database' => new Drivers\DatabaseQueueDriver(
                $config['connection'] ?? null,
                $config['table'] ?? 'jobs'
            ),
            default => new Drivers\SyncQueueDriver(),
        };
    }
}
