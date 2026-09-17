<?php

namespace Liberta\Queue\Drivers;

use Liberta\Queue\Job;
use Liberta\Queue\QueueDriver;

class SyncQueueDriver implements QueueDriver
{
    private array $jobs = [];

    public function push(Job $job, string $queue = 'default'): string
    {
        $id = uniqid('sync_', true);
        $this->jobs[$queue][$id] = $job;
        $job->handle();
        return $id;
    }

    public function pop(string $queue = 'default'): ?Job
    {
        return array_shift($this->jobs[$queue] ?? []);
    }

    public function peek(string $queue = 'default', int $limit = 10): array
    {
        return array_slice($this->jobs[$queue] ?? [], 0, $limit);
    }

    public function size(string $queue = 'default'): int
    {
        return count($this->jobs[$queue] ?? []);
    }

    public function delete(string $id): bool
    {
        return true;
    }

    public function later(int $delay, Job $job, string $queue = 'default'): string
    {
        return $this->push($job, $queue);
    }

    public function release(Job $job, int $delay = 0): string
    {
        return $this->push($job, $job->getQueue());
    }

    public function bulk(array $jobs, string $queue = 'default'): array
    {
        $ids = [];
        foreach ($jobs as $job) {
            $ids[] = $this->push($job, $queue);
        }
        return $ids;
    }
}
