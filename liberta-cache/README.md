# liberta-cache

> Cache abstraction with array, file, and tagged cache support.

## Installation

```bash
composer require chandra/liberta-cache
```

## Usage

### ArrayCache (In-Memory)

```php
use Liberta\Cache\ArrayCache;

$cache = new ArrayCache();

// Basic operations
$cache->set('name', 'Chandra', 3600);
$name = $cache->get('name');           // 'Chandra'
$exists = $cache->has('name');        // true
$cache->delete('name');
$cache->flush();                       // Clear all

// Default values
$value = $cache->get('missing', 'default'); // 'default'

// Remember (cache or compute)
$user = $cache->remember('user:1', 3600, function () {
    return $db->table('users')->where('id', '=', 1)->first();
});

// Atomic operations
$cache->set('counter', 0);
$cache->increment('counter');          // 1
$cache->increment('counter', 5);      // 6
$cache->decrement('counter');          // 5

// Multiple operations
$cache->setMultiple(['a' => 1, 'b' => 2]);
$values = $cache->getMultiple(['a', 'b', 'c']);
$cache->deleteMultiple(['a', 'b']);
```

### FileCache

```php
use Liberta\Cache\FileCache;

$cache = new FileCache('/tmp/cache');

// Same API as ArrayCache
$cache->set('data', $data, 3600);
$value = $cache->get('data');
```

### CacheManager

```php
use Liberta\Cache\CacheManager;

$manager = new CacheManager([
    'array' => ['driver' => 'array'],
    'file' => ['driver' => 'file', 'path' => '/tmp/cache'],
], 'array');

$cache = $manager->store('array');
$fileCache = $manager->store('file');
```

### Tagged Cache

```php
$cache = new ArrayCache();

$tagged = $cache->tags(['users', 'admin']);
$tagged->set('user:1', $userData);

// Flush by tags
$tagged->flush(); // Only flushes items with these tags
```

## API

### CacheStore

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `get()` | `string $key, mixed $default = null` | `mixed` | Get value |
| `set()` | `string $key, mixed $value, int $ttl = 3600` | `bool` | Set value |
| `delete()` | `string $key` | `bool` | Delete value |
| `has()` | `string $key` | `bool` | Check if exists |
| `remember()` | `string $key, int $ttl, callable $callback` | `mixed` | Cache or compute |
| `forget()` | `string $key` | `bool` | Alias for delete |
| `flush()` | — | `bool` | Clear all |
| `increment()` | `string $key, int $value = 1` | `int` | Increment value |
| `decrement()` | `string $key, int $value = 1` | `int` | Decrement value |
| `tags()` | `array $tags` | `TaggedCache` | Get tagged cache |
| `getMultiple()` | `array $keys, mixed $default = null` | `array` | Get multiple |
| `setMultiple()` | `array $values, int $ttl = 3600` | `bool` | Set multiple |
| `deleteMultiple()` | `array $keys` | `bool` | Delete multiple |
