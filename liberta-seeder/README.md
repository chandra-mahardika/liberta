# liberta-seeder

> Database seeder interface and runner for test data.

## Installation

```bash
composer require chandra/liberta-seeder
```

## Usage

### Creating Seeders

```php
use Liberta\Seeder\Seeder;
use Liberta\SqlBuilder\DB;

class UserSeeder implements Seeder
{
    public function run(DB $db): void
    {
        $db->table('users')->insert([
            ['name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin'],
            ['name' => 'User', 'email' => 'user@example.com', 'role' => 'user'],
        ]);
    }

    public function getName(): string
    {
        return 'users';
    }
}
```

### Running Seeders

```php
use Liberta\Seeder\SeederRunner;

$runner = new SeederRunner($db);

// Register seeders
$runner->register('users', new UserSeeder());
$runner->register('posts', new PostSeeder());

// Run specific seeder
$runner->run('users');

// Run all registered seeders
$runner->runAll();

// Run in order
$runner->runInOrder(['users', 'posts']);
```

### Seeding with Dependencies

```php
class PostSeeder implements Seeder
{
    public function __construct(
        private UserSeeder $userSeeder
    ) {}

    public function run(DB $db): void
    {
        // Users must be seeded first
        $users = $db->table('users')->get();

        foreach ($users as $user) {
            $db->table('posts')->insert([
                'user_id' => $user['id'],
                'title' => 'Post by ' . $user['name'],
            ]);
        }
    }

    public function getName(): string
    {
        return 'posts';
    }

    public function getDependencies(): array
    {
        return ['users'];
    }
}
```

## API

### Seeder Interface

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `run()` | `DB $db` | `void` | Execute seeder |
| `getName()` | — | `string` | Get seeder name |
| `getDependencies()` | — | `array` | Get dependent seeders |

### SeederRunner

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `register()` | `string $name, Seeder $seeder` | `void` | Register seeder |
| `run()` | `string $name` | `void` | Run specific seeder |
| `runAll()` | — | `void` | Run all seeders |
| `runInOrder()` | `array $names` | `void` | Run in specified order |
