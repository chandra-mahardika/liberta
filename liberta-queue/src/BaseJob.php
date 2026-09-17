<?php

namespace Liberta\Queue;

abstract class BaseJob implements Job
{
    protected int $retryCount = 3;
    protected int $retryDelay = 60;
    protected string $queue = 'default';

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function getIdentifier(): string
    {
        return get_class($this) . '_' . md5(serialize($this));
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function getRetryDelay(): int
    {
        return $this->retryDelay;
    }

    public function failed(\Throwable $exception): void
    {
        // Default: do nothing, override in subclass
    }
}
