<?php

namespace Liberta\Queue;

class Worker
{
    private bool $running = false;

    public function __construct(
        private QueueManager $queue,
        private int $sleep = 3,
        private int $maxTries = 3
    ) {}

    public function work(string $queue = 'default'): void
    {
        $this->running = true;

        while ($this->running) {
            $job = $this->queue->driver()->pop($queue);

            if ($job === null) {
                sleep($this->sleep);
                continue;
            }

            try {
                $job->handle();
            } catch (\Throwable $e) {
                $this->handleFailedJob($job, $e);
            }
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }

    private function handleFailedJob(Job $job, \Throwable $e): void
    {
        try {
            $job->failed($e);
        } catch (\Throwable $e2) {
            // Logger error
        }
    }
}
