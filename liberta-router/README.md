# liberta-router

> HTTP router with module support and closure-based middleware pipeline.

## Installation

```bash
composer require chandra/liberta-router
```

## Usage

### Basic Routing

```php
use Liberta\Router\Router;

$router = new Router();

// GET routes
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);

// POST routes
$router->post('/users', [UserController::class, 'store']);

// PUT/PATCH/DELETE
$router->put('/users/{id}', [UserController::class, 'update']);
$router->delete('/users/{id}', [UserController::class, 'destroy']);

// Route groups
$router->prefix('api', function ($router) {
    $router->get('/users', [UserController::class, 'index']);
    $router->post('/users', [UserController::class, 'store']);
});

// With middleware
$router->get('/admin', [AdminController::class, 'index'])
    ->middleware('auth', 'admin');
```

### Module System

```php
use Liberta\Router\Module;

class UserModule implements Module
{
    public function name(): string
    {
        return 'users';
    }

    public function routes(): array
    {
        return [
            ['GET', '/', [UserController::class, 'index']],
            ['GET', '/{id}', [UserController::class, 'show']],
            ['POST', '/', [UserController::class, 'store']],
            ['PUT', '/{id}', [UserController::class, 'update']],
            ['DELETE', '/{id}', [UserController::class, 'destroy']],
        ];
    }

    public function middleware(): array
    {
        return ['auth'];
    }
}

// Register modules
$router->registerModule(new UserModule());
```

### Middleware Pipeline

```php
use Liberta\Router\Middleware\MiddlewarePipeline;

$pipeline = new MiddlewarePipeline();

// Add middleware (closures or classes)
$pipeline->pipe(function ($request, $next) {
    $start = microtime(true);
    $response = $next($request);
    $time = (microtime(true) - $start) * 1000;
    $response = $response->withHeader('X-Time', $time . 'ms');
    return $response;
});

$pipeline->pipe(function ($request, $next) {
    if (!$request->bearerToken()) {
        return Response::json(['error' => 'Unauthorized'], 401);
    }
    return $next($request);
});

// Execute
$response = $pipeline->resolve($request, function ($request) {
    return Response::json(['message' => 'Hello']);
});
```

### Dispatching

```php
// From request
$request = Request::fromGlobals();
$response = $router->dispatch($request);

// Send response
header('HTTP/1.1 ' . $response->status());
foreach ($response->headers() as $name => $value) {
    header("{$name}: {$value}");
}
echo $response->body();
```

## API

### Router

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `get()` | `string $path, callable $handler` | `Route` | Register GET route |
| `post()` | `string $path, callable $handler` | `Route` | Register POST route |
| `put()` | `string $path, callable $handler` | `Route` | Register PUT route |
| `delete()` | `string $path, callable $handler` | `Route` | Register DELETE route |
| `prefix()` | `string $prefix, callable $callback` | `void` | Route group with prefix |
| `registerModule()` | `Module $module` | `void` | Register module |
| `dispatch()` | `Request $request` | `Response` | Dispatch request |

### Module Interface

| Method | Returns | Description |
|--------|---------|-------------|
| `name()` | `string` | Module name |
| `routes()` | `array` | Route definitions |
| `middleware()` | `array` | Module middleware |
