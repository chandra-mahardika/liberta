# liberta-queue

> Job queue with sync and database drivers, worker support.

## Installation

```bash
composer require chandra/liberta-queue
```

## Usage

### Creating Jobs

```php
use Liberta\Queue\BaseJob;

class SendWelcomeEmail extends BaseJob
{
    protected string $queue = 'emails';
    protected int $retryCount = 3;
    protected int $retryDelay = 60;

    public function __construct(
        private int $userId
    ) {}

    public function handle(): void
    {
        $user = $db->table('users')->where('id', '=', $this->userId)->first();
        // Send email...
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Welcome email failed: " . $exception->getMessage());
    }
}
```

### Dispatching Jobs

```php
use Liberta\Queue\QueueManager;
use Liberta\Queue\Dispatcher;

$manager = new QueueManager([
    'sync' => ['driver' => 'sync'],
    'database' => ['driver' => 'database', 'table' => 'jobs'],
], 'sync');

$dispatcher = new Dispatcher($manager);

// Dispatch immediately
$dispatcher->dispatch(new SendWelcomeEmail($userId));

// Dispatch to specific queue
$dispatcher->dispatch(new SendWelcomeEmail($userId), 'emails');

// Dispatch with delay (seconds)
$dispatcher->later(300, new SendWelcomeEmail($userId));

// Dispatch now (bypass queue)
$dispatcher->dispatchNow(new SendWelcomeEmail($userId));
```

### Queue Manager

```php
$manager = new QueueManager($config, 'database');

// Push job
$id = $manager->push(new SendWelcomeEmail($userId), 'emails');

// Push with delay
$id = $manager->later(300, new SendWelcomeEmail($userId));

// Push to specific queue
$id = $manager->laterOn('emails', 300, new SendWelcomeEmail($userId));
```

### Queue Drivers

#### Sync Driver

```php
// Runs immediately (for testing)
$manager = new QueueManager([], 'sync');
```

#### Database Driver

```php
// Requires jobs table migration
$manager = new QueueManager([
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
    ],
], 'database');

// Migration
Schema::create('jobs', function ($table) {
    $table->id();
    $table->string('queue');
    $table->text('payload');
    $table->integer('attempts');
    $table->timestamp('reserved_at')->nullable();
    $table->timestamp('available_at');
    $table->timestamp('created_at');
});
```

### Worker

```php
use Liberta\Queue\Worker;

$worker = new Worker($manager, sleep: 3, maxTries: 3);

// Run worker (blocks)
$worker->work('emails');

// Stop worker
$worker->stop();
```

## API

### Job Interface

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `handle()` | — | `void` | Execute job |
| `failed()` | `Throwable $exception` | `void` | Handle failure |
| `getQueue()` | — | `string` | Get queue name |
| `getIdentifier()` | — | `string` | Get unique ID |
| `getRetryCount()` | — | `int` | Get max retries |
| `getRetryDelay()` | — | `int` | Get retry delay |

### Dispatcher

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `dispatch()` | `Job $job, string $queue = 'default'` | `string` | Dispatch job |
| `later()` | `int $delay, Job $job, string $queue` | `string` | Dispatch with delay |
| `dispatchNow()` | `Job $job` | `void` | Execute immediately |

### QueueManager

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `push()` | `Job $job, string $queue = 'default'` | `string` | Push job |
| `later()` | `int $delay, Job $job, string $queue` | `string` | Push with delay |
| `laterOn()` | `string $queue, int $delay, Job $job` | `string` | Push to queue with delay |
