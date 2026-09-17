# liberta-migration

> Database migration system with schema builder and column definitions.

## Installation

```bash
composer require chandra/liberta-migration
```

## Usage

### Creating Migrations

```php
use Liberta\Migration\Migration;
use Liberta\Migration\Schema;

class CreateUsersTable implements Migration
{
    public function up(Schema $schema): void
    {
        $schema->create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(Schema $schema): void
    {
        $schema->dropIfExists('users');
    }
}
```

### Column Types

```php
$table->bigIncrements('id');     // BIGINT AUTO_INCREMENT PRIMARY KEY
$table->increments('id');        // INT AUTO_INCREMENT PRIMARY KEY
$table->string('name');          // VARCHAR(255)
$table->string('name', 100);    // VARCHAR(100)
$table->text('bio');             // TEXT
$table->integer('age');          // INTEGER
$table->bigInteger('views');     // BIGINT
$table->boolean('active');       // BOOLEAN
$table->decimal('price', 8, 2); // DECIMAL(8,2)
$table->timestamp('created_at'); // TIMESTAMP
$table->timestamps();            // created_at + updated_at
$table->softDeletes();           // deleted_at
$table->json('data');            // JSON
$table->uuid('uuid');            // UUID
$table->enum('status', ['active', 'inactive']); // ENUM
```

### Column Modifiers

```php
$table->string('email')->nullable();           // NULL allowed
$table->string('email')->unique();             // UNIQUE index
$table->string('name')->default('unknown');    // DEFAULT value
$table->integer('age')->unsigned();            // UNSIGNED
$table->string('token')->index();              // INDEX
$table->foreign('user_id')->references('id')->on('users'); // FOREIGN KEY
```

### Running Migrations

```php
use Liberta\Migration\Migrator;

$migrator = new Migrator($connection);

// Run all pending migrations
$migrator->run('migrations/');

// Rollback last batch
$migrator->rollback('migrations/');

// Rollback specific migration
$migrator->rollback('migrations/2026_07_20_create_users.php');

// Status
$status = $migrator->getStatus(); // ['ran' => [...], 'pending' => [...]]
```

### Altering Tables

```php
use Liberta\Migration\Schema;

Schema::table('users', function ($table) {
    $table->string('phone')->nullable();
    $table->dropColumn('legacy_field');
    $table->rename('old_users');
});
```

## API

### Schema

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `create()` | `string $table, callable $callback` | `void` | Create table |
| `drop()` | `string $table` | `void` | Drop table |
| `dropIfExists()` | `string $table` | `void` | Drop if exists |
| `table()` | `string $table, callable $callback` | `void` | Alter table |

### Table

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `bigIncrements()` | `string $column` | `ColumnBuilder` | BIGINT AUTO_INCREMENT |
| `string()` | `string $column, int $length = 255` | `ColumnBuilder` | VARCHAR |
| `text()` | `string $column` | `ColumnBuilder` | TEXT |
| `integer()` | `string $column` | `ColumnBuilder` | INTEGER |
| `boolean()` | `string $column` | `ColumnBuilder` | BOOLEAN |
| `decimal()` | `string $column, int $precision, int $scale` | `ColumnBuilder` | DECIMAL |
| `timestamps()` | — | `void` | Add created_at + updated_at |
| `softDeletes()` | — | `void` | Add deleted_at |
| `foreign()` | `string $column` | `ForeignKeyBuilder` | Foreign key constraint |
| `index()` | `string $column` | `void` | Add index |
| `unique()` | `string $column` | `void` | Add unique index |
| `dropColumn()` | `string $column` | `void` | Drop column |

### Migrator

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `run()` | `string $path` | `array` | Run pending migrations |
| `rollback()` | `string $path, int $steps = 1` | `array` | Rollback migrations |
| `getStatus()` | — | `array` | Get migration status |
| `getApplied()` | — | `array` | Get applied migrations |
