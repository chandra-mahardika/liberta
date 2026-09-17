# liberta-container

> Lightweight DI container for Liberta microservices. Explicit binding, no magic scanning.

## Installation

```bash
composer require chandra/liberta-container
```

## Usage

### Basic Usage

```php
use Liberta\Container\Container;

$container = new Container();

// Bind factory (new instance each time)
$container->bind('logger', fn() => new FileLogger('/tmp/logs'));

// Singleton (same instance always)
$container->singleton('db', fn() => new PDO('mysql:host=localhost', 'root', ''));

// Instance (specific object)
$container->instance('config', $config);

// Resolve
$logger = $container->resolve('logger');
$db = $container->resolve('db');
```

### Auto-Construct via Reflection

```php
class UserService
{
    public function __construct(
        private UserRepository $repo,
        private TokenService $tokenService
    ) {}
}

// If dependencies are bound, Container auto-resolves via reflection
$container->bind(UserRepository::class, fn() => new UserRepository($db));
$container->bind(TokenService::class, fn() => new TokenService($jwt));

$service = $container->make(UserService::class);
```

### Make Shortcut

```php
// resolve() throws on missing, make() uses auto-construct
$service = $container->make(UserService::class);
```

## API

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `bind()` | `string $abstract, callable $concrete` | `void` | Bind factory |
| `singleton()` | `string $abstract, callable $concrete` | `void` | Bind singleton |
| `instance()` | `string $abstract, object $instance` | `void` | Bind instance |
| `resolve()` | `string $abstract` | `mixed` | Resolve binding (throws if missing) |
| `make()` | `string $class` | `mixed` | Auto-construct via reflection |
