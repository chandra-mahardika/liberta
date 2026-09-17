<?php

namespace Liberta\Queue;

interface Job
{
    public function handle(): void;
    public function failed(\Throwable $exception): void;
    public function getQueue(): string;
    public function getIdentifier(): string;
    public function getRetryCount(): int;
    public function getRetryDelay(): int;
}
