<?php

namespace Liberta\Queue;

interface QueueDriver
{
    public function push(Job $job, string $queue = 'default'): string;
    public function pop(string $queue = 'default'): ?Job;
    public function peek(string $queue = 'default', int $limit = 10): array;
    public function size(string $queue = 'default'): int;
    public function delete(string $id): bool;
    public function later(int $delay, Job $job, string $queue = 'default'): string;
    public function release(Job $job, int $delay = 0): string;
    public function bulk(array $jobs, string $queue = 'default'): array;
}
