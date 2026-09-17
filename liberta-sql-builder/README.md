# liberta-sql-builder

> Fluent query builder with multi-dialect SQL compilation, transactions, and query caching.

## Installation

```bash
composer require chandra/liberta-sql-builder
```

## Usage

### Connection Setup

```php
use Liberta\SqlBuilder\DB;

// Singleton instance
$db = DB::getInstance();

// Or create new instance
$db = new DB($pdo);
```

### SELECT Queries

```php
// Simple select
$users = $db->table('users')->get();

// With conditions
$users = $db->table('users')
    ->where('status', '=', 'active')
    ->where('age', '>', 18)
    ->orderBy('name')
    ->limit(10)
    ->get();

// Single record
$user = $db->table('users')->where('id', '=', 1)->first();

// Select specific columns
$users = $db->table('users')
    ->select('id', 'name', 'email')
    ->get();

// Count
$count = $db->table('users')->where('status', '=', 'active')->count();

// Pluck (single column)
$emails = $db->table('users')->pluck('email');
```

### INSERT Queries

```php
// Insert single
$id = $db->table('users')->insert([
    'name' => 'Chandra',
    'email' => 'chandra.libertania@gmail.com',
    'created_at' => date('Y-m-d H:i:s'),
]);

// Insert multiple
$db->table('users')->insert([
    ['name' => 'User 1', 'email' => 'user1@example.com'],
    ['name' => 'User 2', 'email' => 'user2@example.com'],
]);
```

### UPDATE Queries

```php
$db->table('users')
    ->where('id', '=', 1)
    ->update([
        'name' => 'Updated Name',
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
```

### DELETE Queries

```php
$db->table('users')->where('id', '=', 1)->delete();
```

### Transactions

```php
$db->transaction(function ($db) {
    $db->table('accounts')->where('id', '=', 1)->update(['balance' => 900]);
    $db->table('accounts')->where('id', '=', 2)->update(['balance' => 1100]);
    $db->table('transactions')->insert([
        'from' => 1,
        'to' => 2,
        'amount' => 200,
    ]);
});

// Manual transaction
$db->beginTransaction();
try {
    // ... operations
    $db->commit();
} catch (\Throwable $e) {
    $db->rollback();
    throw $e;
}
```

### Raw Expressions

```php
$db->table('users')
    ->select(DB::raw('COUNT(*) as total'))
    ->where('status', '=', 'active')
    ->get();

// Raw WHERE
$db->table('users')
    ->whereRaw('age > ? AND status = ?', [18, 'active'])
    ->get();
```

### Query Caching

```php
use Liberta\SqlBuilder\Cache\QueryCache;
use Liberta\Cache\ArrayCache;

$cache = new QueryCache(new ArrayCache());

// Cache query result
$key = $cache->generateKey('SELECT * FROM users WHERE id = ?', [1]);
$result = $cache->get($key);

if ($result === null) {
    $result = $db->table('users')->where('id', '=', 1)->first();
    $cache->set($key, $result, 300);
}
```

### Query Profiling

```php
use Liberta\SqlBuilder\Profiler\Profiler;

$profiler = new Profiler();

$profiler->start('fetch_users');
$users = $db->table('users')->get();
$profiler->stop('fetch_users');

$report = $profiler->getReport();
// ['sections' => [...], 'queries' => [...], 'memory' => [...]]
```

## API

### Query Builder

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `table()` | `string $table` | `static` | Set table |
| `select()` | `string ...$columns` | `static` | Select columns |
| `where()` | `string $col, string $op, mixed $val` | `static` | Add WHERE clause |
| `whereRaw()` | `string $sql, array $bindings` | `static` | Raw WHERE |
| `orWhere()` | `string $col, string $op, mixed $val` | `static` | OR WHERE |
| `orderBy()` | `string $column, string $dir = 'ASC'` | `static` | ORDER BY |
| `limit()` | `int $limit` | `static` | LIMIT |
| `offset()` | `int $offset` | `static` | OFFSET |
| `get()` | — | `array` | Execute and get results |
| `first()` | — | `?array` | Get first row |
| `count()` | — | `int` | Count rows |
| `pluck()` | `string $column` | `array` | Get column values |
| `insert()` | `array $data` | `int` | Insert row(s) |
| `update()` | `array $data` | `bool` | Update rows |
| `delete()` | — | `bool` | Delete rows |
| `transaction()` | `callable $callback` | `mixed` | Run in transaction |
| `beginTransaction()` | — | `void` | Start transaction |
| `commit()` | — | `void` | Commit transaction |
| `rollback()` | — | `void` | Rollback transaction |
| `raw()` | `string $expression` | `RawExpression` | Raw SQL expression |
