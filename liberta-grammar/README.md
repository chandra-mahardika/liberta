# liberta-grammar

> SQL grammar abstraction for multiple database dialects (MySQL, PostgreSQL, SQLite, SQL Server).

## Installation

```bash
composer require chandra/liberta-grammar
```

## Usage

### Selecting Grammar

```php
use Liberta\Grammar\MysqlGrammar;
use Liberta\Grammar\PostgresGrammar;
use Liberta\Grammar\SqliteGrammar;
use Liberta\Grammar\SqlServerGrammar;

// Auto-detect from connection
$grammar = Grammar::forDriver('mysql');
$grammar = Grammar::forDriver('pgsql');
$grammar = Grammar::forDriver('sqlite');
$grammar = Grammar::forDriver('sqlsrv');
```

### Compile Queries

```php
$grammar = new MysqlGrammar();

// Compile SELECT
$sql = $grammar->compileSelect([
    'table' => 'users',
    'columns' => ['id', 'name'],
    'wheres' => [['column' => 'status', 'operator' => '=', 'value' => 'active']],
    'orders' => [['column' => 'name', 'direction' => 'ASC']],
    'limit' => 10,
]);
// SELECT `id`, `name` FROM `users` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10

// Compile INSERT
$sql = $grammar->compileInsert('users', ['name', 'email']);
// INSERT INTO `users` (`name`, `email`) VALUES (?, ?)

// Compile UPDATE
$sql = $grammar->compileUpdate('users', ['name', 'email']);
// UPDATE `users` SET `name` = ?, `email` = ? WHERE `id` = ?

// Compile DELETE
$sql = $grammar->compileDelete('users');
// DELETE FROM `users` WHERE `id` = ?
```

### Column Compilation

```php
$grammar = new MysqlGrammar();

$grammar->compileColumn('name', 'string', true);
// `name` VARCHAR(255) NOT NULL

$grammar->compileColumn('age', 'integer', false);
// `age` INTEGER NULL

$grammar->compileColumn('id', 'bigIncrements');
// `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
```

## Supported Dialects

| Grammar | Driver | Quoting |
|---------|--------|---------|
| `MysqlGrammar` | mysql | Backticks `` ` `` |
| `PostgresGrammar` | pgsql | Double quotes `"` |
| `SqliteGrammar` | sqlite | Double quotes `"` |
| `SqlServerGrammar` | sqlsrv | Brackets `[]` |

## Column Types

| Type | MySQL | PostgreSQL | SQLite | SQL Server |
|------|-------|------------|--------|------------|
| string | VARCHAR(255) | VARCHAR(255) | VARCHAR(255) | NVARCHAR(255) |
| text | TEXT | TEXT | TEXT | NVARCHAR(MAX) |
| integer | INTEGER | INTEGER | INTEGER | INT |
| bigIncrements | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | BIGSERIAL PRIMARY KEY | INTEGER PRIMARY KEY AUTOINCREMENT | BIGINT IDENTITY PRIMARY KEY |
| boolean | TINYINT(1) | BOOLEAN | BOOLEAN | BIT |
| timestamp | TIMESTAMP | TIMESTAMP | DATETIME | DATETIME2 |
| decimal | DECIMAL(8,2) | DECIMAL(8,2) | DECIMAL(8,2) | DECIMAL(8,2) |
