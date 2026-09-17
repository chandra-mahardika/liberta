# liberta-cli

> Console application with artisan-like commands for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-cli
```

## Usage

### Creating Commands

```php
use Liberta\Cli\BaseCommand;
use Liberta\Cli\Application;

class HelloCommand extends BaseCommand
{
    protected string $name = 'hello';
    protected string $description = 'Say hello';
    protected string $signature = 'hello {name? : User name}';

    public function handle(): int
    {
        $name = $this->argument('name') ?? 'World';
        $this->info("Hello, {$name}!");
        return 0;
    }
}
```

### Registering Commands

```php
use Liberta\Cli\Application;

$app = new Application();

// Register commands
$app->register(new HelloCommand());
$app->register(new MigrateCommand());
$app->register(new SeedCommand());
$app->register(new MakeCommand());

// Run
$app->run();
```

### Built-in Commands

#### MigrateCommand

```bash
php liberta migrate              # Run pending migrations
php liberta migrate:rollback     # Rollback last batch
php liberta migrate:status       # Show migration status
```

#### SeedCommand

```bash
php liberta seed                 # Run all seeders
php liberta seed users           # Run specific seeder
php liberta seed --class=UserSeeder  # Run specific class
```

#### MakeCommand

```bash
php liberta make:controller UserController
php liberta make:migration create_users_table
php liberta make:seeder UserSeeder
php liberta make:module UserModule
php liberta make:model User
php liberta make:service UserService
php liberta make:repository UserRepository
```

### Output Helpers

```php
class MyCommand extends BaseCommand
{
    public function handle(): int
    {
        // Colored output
        $this->info('Success message');
        $this->error('Error message');
        $this->warning('Warning message');
        $this->line('Normal text');

        // Tables
        $this->table(['Name', 'Email'], [
            ['Chandra', 'chandra.libertania@gmail.com'],
            ['User', 'user@example.com'],
        ]);

        // Progress bar
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        for ($i = 0; $i < 100; $i++) {
            $bar->advance();
        }
        $bar->finish();

        // Confirm
        $confirmed = $this->confirm('Do you want to continue?');

        // Ask
        $name = $this->ask('What is your name?');
        $secret = $this->secret('Enter password:');

        // Choice
        $option = $this->choice('Pick one', ['A', 'B', 'C']);

        return 0;
    }
}
```

## API

### Application

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `register()` | `BaseCommand $command` | `void` | Register command |
| `run()` | — | `int` | Run application |

### BaseCommand

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `argument()` | `string $name` | `?string` | Get argument |
| `option()` | `string $name` | `mixed` | Get option |
| `info()` | `string $message` | `void` | Green output |
| `error()` | `string $message` | `void` | Red output |
| `warning()` | `string $message` | `void` | Yellow output |
| `line()` | `string $message` | `void` | Normal output |
| `confirm()` | `string $question` | `bool` | Yes/no confirm |
| `ask()` | `string $question` | `string` | Ask question |
| `secret()` | `string $question` | `string` | Ask hidden input |
| `choice()` | `string $question, array $options` | `string` | Choice list |
| `table()` | `array $headers, array $rows` | `void` | Display table |
