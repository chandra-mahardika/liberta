# liberta-log

> Simple logging interface with file-based and null implementations.

## Installation

```bash
composer require chandra/liberta-log
```

## Usage

### FileLogger

```php
use Liberta\Log\FileLogger;

$logger = new FileLogger('/var/logs');

$logger->info('User created', ['user_id' => 123]);
$logger->error('Payment failed', ['order_id' => 456, 'amount' => 99.99]);
$logger->warning('Stock low', ['product_id' => 789]);
$logger->debug('Query executed', ['sql' => 'SELECT * FROM users']);
```

### NullLogger

```php
use Liberta\Log\NullLogger;

$logger = new NullLogger();
$logger->info('This goes nowhere'); // No-op
```

### Logger Interface

```php
use Liberta\Log\Logger;

class CustomLogger implements Logger
{
    public function info(string $message, array $context = []): void {}
    public function error(string $message, array $context = []): void {}
    public function warning(string $message, array $context = []): void {}
    public function debug(string $message, array $context = []): void {}
}
```

## Log Levels

| Level | Method | Description |
|-------|--------|-------------|
| DEBUG | `debug()` | Detailed debug information |
| INFO | `info()` | General information |
| WARNING | `warning()` | Warning messages |
| ERROR | `error()` | Error messages |

## File Structure

```
/var/logs/
├── 2026-07-20.log
├── 2026-07-21.log
└── ...
```
