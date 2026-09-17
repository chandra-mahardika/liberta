<?php

namespace Liberta\Scheduler;

use Liberta\Scheduler\BaseTask;

class CallableTask extends BaseTask
{
    public function __construct(
        private string $name,
        private callable $callback
    ) {}

    public function run(): void
    {
        ($this->callback)();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
