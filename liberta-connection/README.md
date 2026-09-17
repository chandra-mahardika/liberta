# liberta-connection

> Multi-database connection manager with connection pooling.

## Installation

```bash
composer require chandra/liberta-connection
```

## Usage

### Connection Manager

```php
use Liberta\Connection\ConnectionManager;

$manager = new ConnectionManager([
    'mysql' => [
        'dsn' => 'mysql:host=localhost;dbname=main_db',
        'username' => 'root',
        'password' => 'secret',
    ],
    'pgsql' => [
        'dsn' => 'pgsql:host=localhost;dbname=analytics_db',
        'username' => 'postgres',
        'password' => 'secret',
    ],
]);

// Get connection
$mysql = $manager->connection('mysql');
$pgsql = $manager->connection('pgsql');

// Default connection
$default = $manager->connection();

// Check if connected
$manager->isConnected('mysql');

// Disconnect
$manager->disconnect('mysql');
$manager->disconnectAll();
```

### Connection Pool

```php
use Liberta\Connection\ConnectionPool;

$pool = new ConnectionPool(
    dsn: 'mysql:host=localhost;dbname=mydb',
    username: 'root',
    password: 'secret',
    options: [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
    minConnections: 2,
    maxConnections: 10,
    lifetime: 3600
);

// Get connection from pool
$pdo = $pool->getConnection();

// Use connection
$stmt = $pdo->query('SELECT * FROM users');
$users = $stmt->fetchAll();

// Release back to pool
$pool->release($pdo);

// Pool stats
$pool->getConnectionCount();    // Active connections
$pool->getAvailableCount();     // Available slots

// Close all connections
$pool->closeAll();
```

## API

### ConnectionManager

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `connection()` | `?string $name = null` | `PDO` | Get connection |
| `isConnected()` | `string $name` | `bool` | Check connection status |
| `disconnect()` | `string $name` | `void` | Disconnect specific |
| `disconnectAll()` | — | `void` | Disconnect all |

### ConnectionPool

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `getConnection()` | — | `PDO` | Get connection from pool |
| `release()` | `PDO $connection` | `void` | Release back to pool |
| `getConnectionCount()` | — | `int` | Active connections |
| `getAvailableCount()` | — | `int` | Available slots |
| `closeAll()` | — | `void` | Close all connections |
