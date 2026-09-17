<?php

declare(strict_types=1);

namespace Liberta\Scheduler\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\Scheduler\Scheduler;
use Liberta\Scheduler\CallableTask;

class SchedulerTest extends TestCase
{
    public function testAddTask(): void
    {
        $scheduler = new Scheduler();
        $task = new CallableTask('test', fn() => null);

        $scheduler->add($task);

        $this->assertCount(1, $scheduler->getTasks());
    }

    public function testTaskRunsWhenDue(): void
    {
        $scheduler = new Scheduler();
        $executed = false;

        $task = new CallableTask('test', function () use (&$executed) {
            $executed = true;
        });
        $task->cron('* * * * *');

        $scheduler->add($task);
        $scheduler->run();

        $this->assertTrue($executed);
    }

    public function testTaskSkipsWhenDisabled(): void
    {
        $scheduler = new Scheduler();
        $executed = false;

        $task = new CallableTask('test', function () use (&$executed) {
            $executed = true;
        });
        $task->cron('* * * * *')->disable();

        $scheduler->add($task);
        $scheduler->run();

        $this->assertFalse($executed);
    }

    public function testGetDueTasks(): void
    {
        $scheduler = new Scheduler();

        $task1 = new CallableTask('enabled', fn() => null);
        $task1->cron('* * * * *');

        $task2 = new CallableTask('disabled', fn() => null);
        $task2->cron('* * * * *')->disable();

        $scheduler->add($task1);
        $scheduler->add($task2);

        $due = $scheduler->getDueTasks();

        $this->assertCount(1, $due);
        $this->assertEquals('enabled', $due[0]->getName());
    }
}
