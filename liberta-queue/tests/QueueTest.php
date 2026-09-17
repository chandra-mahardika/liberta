<?php

declare(strict_types=1);

namespace Liberta\Queue\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\Queue\BaseJob;
use Liberta\Queue\Drivers\SyncQueueDriver;
use Liberta\Queue\Dispatcher;
use Liberta\Queue\QueueManager;

class QueueTest extends TestCase
{
    public function testSyncQueuePush(): void
    {
        $driver = new SyncQueueDriver();
        $job = new TestJob();

        $id = $driver->push($job);

        $this->assertNotEmpty($id);
        $this->assertTrue($job->handled);
    }

    public function testSyncQueueSize(): void
    {
        $driver = new SyncQueueDriver();
        $this->assertEquals(0, $driver->size());
    }

    public function testQueueManagerSync(): void
    {
        $manager = new QueueManager([], 'sync');
        $job = new TestJob();

        $id = $manager->push($job);

        $this->assertNotEmpty($id);
        $this->assertTrue($job->handled);
    }

    public function testDispatcher(): void
    {
        $manager = new QueueManager([], 'sync');
        $dispatcher = new Dispatcher($manager);
        $job = new TestJob();

        $dispatcher->dispatch($job);

        $this->assertTrue($job->handled);
    }
}

class TestJob extends BaseJob
{
    public bool $handled = false;

    public function handle(): void
    {
        $this->handled = true;
    }
}
