# liberta-scheduler

> Cron-like task scheduler with interval and cron expression support.

## Installation

```bash
composer require chandra/liberta-scheduler
```

## Usage

### Creating Tasks

```php
use Liberta\Scheduler\BaseTask;

class CleanupTask extends BaseTask
{
    protected string $cronExpression = '0 2 * * *'; // Daily at 2 AM

    public function run(): void
    {
        // Cleanup logic
        $db->table('sessions')
            ->where('last_activity', '<', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->delete();
    }
}

class SyncDataTask extends BaseTask
{
    protected int $interval = 300; // Every 5 minutes

    public function run(): void
    {
        // Sync logic
    }
}
```

### Using Callable Tasks

```php
use Liberta\Scheduler\CallableTask;

$task = new CallableTask('cleanup', function () {
    // Cleanup logic
});
$task->cron('0 2 * * *');

$task2 = new CallableTask('sync', function () {
    // Sync logic
});
$task2->everyMinutes(5);
```

### Scheduling

```php
use Liberta\Scheduler\Scheduler;

$scheduler = new Scheduler();

// Add tasks
$scheduler->add(new CleanupTask());
$scheduler->add(new SyncDataTask());
$scheduler->add($task);

// Run (checks all tasks)
$scheduler->run();

// Get due tasks
$due = $scheduler->getDueTasks();
```

### Cron Expressions

```php
$task = new CleanupTask();

// Basic patterns
$task->cron('* * * * *');        // Every minute
$task->cron('0 * * * *');        // Every hour
$task->cron('0 0 * * *');        // Every day at midnight
$task->cron('0 0 * * 0');        // Every Sunday
$task->cron('0 0 1 * *');        // First day of month

// Ranges
$task->cron('0 9-17 * * *');     // Every hour from 9am-5pm

// Lists
$task->cron('0 0 * * 1,3,5');    // Mon, Wed, Fri

// Steps
$task->cron('*/5 * * * *');      // Every 5 minutes
$task->cron('0 */2 * * *');      // Every 2 hours
```

### Interval Helpers

```php
$task = new SyncDataTask();

$task->everyMinutes(5);    // Every 5 minutes
$task->everyHour();        // Every hour
$task->daily();            // Every day at midnight
$task->weekly();           // Every Sunday
$task->monthly();          // First of month
```

### Enable/Disable

```php
$task = new CleanupTask();
$task->disable();  // Skip this task
$task->enable();   // Re-enable
```

## API

### Task Interface

| Method | Returns | Description |
|--------|---------|-------------|
| `run()` | `void` | Execute task |
| `getName()` | `string` | Get task name |
| `getSchedule()` | `string` | Get schedule description |
| `isEnabled()` | `bool` | Check if enabled |
| `getInterval()` | `?int` | Get interval in seconds |
| `getCronExpression()` | `?string` | Get cron expression |
| `getLastRunAt()` | `?\DateTimeImmutable` | Get last run time |

### BaseTask

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `cron()` | `string $expression` | `static` | Set cron expression |
| `everyMinutes()` | `int $minutes` | `static` | Set interval |
| `everyHour()` | — | `static` | Every hour |
| `daily()` | — | `static` | Every day |
| `weekly()` | — | `static` | Every week |
| `monthly()` | — | `static` | Every month |
| `disable()` | — | `static` | Disable task |
| `enable()` | — | `static` | Enable task |

### Scheduler

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `add()` | `Task $task` | `void` | Add task |
| `run()` | — | `void` | Run all due tasks |
| `getDueTasks()` | — | `array` | Get tasks due to run |
| `getTasks()` | — | `array` | Get all tasks |
