<?php

namespace Liberta\Queue;

class Dispatcher
{
    public function __construct(
        private QueueManager $queue
    ) {}

    public function dispatch(Job $job, string $queue = 'default'): string
    {
        return $this->queue->push($job, $queue);
    }

    public function later(int $delay, Job $job, string $queue = 'default'): string
    {
        return $this->queue->later($delay, $job, $queue);
    }

    public function dispatchNow(Job $job): void
    {
        $job->handle();
    }
}
