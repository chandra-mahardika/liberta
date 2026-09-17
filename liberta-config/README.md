# liberta-config

> Dot-notation configuration loader for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-config
```

## Usage

### Basic Usage

```php
use Liberta\Config\Config;

// Load single file
$config = Config::fromFile('config/app.php');

// Load from directory
$config = Config::fromDirectory('config/');

// Access with dot-notation
$host = $config->get('database.host', 'localhost');
$port = $config->get('database.port', 3306);

// Check if key exists
if ($config->has('database.host')) {
    // ...
}

// Get all config
$all = $config->all();

// Set value
$config->set('database.host', '127.0.0.1');
```

### Config File Format

```php
// config/app.php
return [
    'name' => 'My App',
    'env' => 'production',
    'debug' => false,
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'my_database',
    ],
];
```

### Environment Variables

```php
$config = Config::fromFile('config/app.php');

// Override with env vars
// DB_HOST=127.0.0.1
$host = $config->get('database.host');
```

## API

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `fromFile()` | `string $path` | `Config` | Load config from file |
| `fromDirectory()` | `string $path` | `Config` | Load all PHP files from directory |
| `get()` | `string $key, mixed $default = null` | `mixed` | Get value by dot-notation key |
| `set()` | `string $key, mixed $value` | `void` | Set value by dot-notation key |
| `has()` | `string $key` | `bool` | Check if key exists |
| `all()` | — | `array` | Get all config values |
