# liberta-http

> Immutable HTTP request/response objects with performance middleware.

## Installation

```bash
composer require chandra/liberta-http
```

## Usage

### Request

```php
use Liberta\Http\Request;

// Create from globals
$request = Request::fromGlobals();

// Access data
$path = $request->path();              // '/users/123'
$method = $request->method();          // 'GET'
$query = $request->query();            // ['page' => 1]
$body = $request->body();              // ['name' => 'Chandra']
$all = $request->all();                // Merged query + body
$ip = $request->ip();                  // Client IP
$host = $request->host();              // Hostname
$scheme = $request->scheme();          // 'https'
$url = $request->url();                // Full URL

// Specific values
$name = $request->get('name');         // From query or body
$page = $request->query('page', 1);   // With default
$token = $request->bearerToken();     // Authorization Bearer
$auth = $request->header('Authorization');

// Check
$request->has('name');
$request->isPost();
$request->isGet();
$request->isAjax();
```

### Response

```php
use Liberta\Http\Response;

// Basic responses
$response = new Response();
$response = Response::json(['users' => $users]);
$response = Response::json($data, 200);
$response = Response::html('<h1>Hello</h1>');
$response = Response::redirect('/login', 302);
$response = Response::noContent();
$response = Response::notFound();
$response = Response::forbidden();

// Immutable modifiers (returns new instance)
$response = $response->withStatus(201);
$response = $response->withHeader('Content-Type', 'application/json');
$response = $response->withBody('{"name": "Chandra"}');

// Getters
$response->status();       // 200
$response->headers();      // ['Content-Type' => '...']
$response->body();         // Body content
```

### Performance Middleware

```php
use Liberta\Http\Middleware\PerformanceMiddleware;
use Liberta\SqlBuilder\Profiler\Profiler;

$profiler = new Profiler();
$middleware = new PerformanceMiddleware($profiler);

// Adds headers:
// X-Request-Time: 12.34ms
// X-Query-Count: 5
// X-Query-Time: 8.90ms
```

### Security Headers Middleware

```php
use Liberta\Http\Middleware\SecurityHeadersMiddleware;

// Default security headers
$middleware = new SecurityHeadersMiddleware();
// Adds: HSTS, X-Frame-Options: DENY, X-Content-Type-Options: nosniff,
//       X-XSS-Protection, Referrer-Policy, Permissions-Policy, Cache-Control

// Custom overrides
$middleware = new SecurityHeadersMiddleware([
    'X-Frame-Options' => 'SAMEORIGIN',
    'Strict-Transport-Security' => 'max-age=63072000',
]);
```

### Rate Limiting Middleware

```php
use Liberta\Http\Middleware\RateLimitMiddleware;

// Default: 60 requests per minute per IP
$middleware = new RateLimitMiddleware();

// Custom limits
$middleware = new RateLimitMiddleware(
    maxAttempts: 10,
    windowSeconds: 60,
    storagePath: '/tmp/liberta_rate_limit'
);

// Adds headers: X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset
// Returns 429 with Retry-After header when limit exceeded
```

### CORS Middleware

```php
use Liberta\Http\Middleware\CorsMiddleware;

// Allow all origins (development)
$middleware = new CorsMiddleware();

// Restrict origins (production)
$middleware = new CorsMiddleware(
    allowedOrigins: ['https://app.example.com', 'https://admin.example.com'],
    allowedMethods: ['GET', 'POST', 'PUT', 'DELETE'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Tenant-ID'],
    allowCredentials: true,
    maxAge: 86400
);

// Handles OPTIONS preflight automatically
```

### Validation Middleware

```php
use Liberta\Http\Middleware\ValidationMiddleware;

$middleware = new ValidationMiddleware(
    rules: [
        'name'  => 'required|string|max:255',
        'email' => 'required|email',
        'age'   => 'integer|min:0|max:150',
    ],
    messages: [
        'email.required' => 'Email wajib diisi',
    ],
    source: 'body'  // 'body', 'query', or 'all'
);

// Returns 422 with structured error response on validation failure
```

### Validator (Standalone)

```php
use Liberta\Http\Validation\Validator;

$validator = new Validator();

try {
    $validator->validate($data, [
        'name'  => 'required|string|max:255',
        'email' => 'required|email',
    ]);
} catch (ValidationException $e) {
    $errors = $e->errors();      // ['email' => ['Email invalid']]
    $first  = $e->firstError();  // 'Email invalid'
}

// Available rules: required, string, integer, float, email, min, max,
// between, in, alpha, alphaNum, alphaDash, date, url, uuid, regex,
// confirmed, array

## API

### Request

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `fromGlobals()` | — | `static` | Create from PHP globals |
| `path()` | — | `string` | URL path |
| `method()` | — | `string` | HTTP method |
| `query()` | `?string $key = null, mixed $default = null` | `mixed` | Query parameters |
| `body()` | `?string $key = null, mixed $default = null` | `mixed` | Request body |
| `all()` | — | `array` | All input data |
| `get()` | `string $key, mixed $default = null` | `mixed` | Get value |
| `has()` | `string $key` | `bool` | Check if exists |
| `header()` | `string $name, ?string $default = null` | `?string` | Get header |
| `bearerToken()` | — | `?string` | Get Bearer token |
| `ip()` | — | `string` | Client IP |
| `host()` | — | `string` | Hostname |
| `url()` | — | `string` | Full URL |
| `isGet()` | — | `bool` | Is GET request |
| `isPost()` | — | `bool` | Is POST request |
| `isAjax()` | — | `bool` | Is AJAX request |

### Response

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `json()` | `mixed $data, int $status = 200` | `static` | JSON response |
| `html()` | `string $content, int $status = 200` | `static` | HTML response |
| `redirect()` | `string $url, int $status = 302` | `static` | Redirect response |
| `noContent()` | — | `static` | 204 No Content |
| `notFound()` | — | `static` | 404 Not Found |
| `withStatus()` | `int $status` | `static` | Set status (immutable) |
| `withHeader()` | `string $name, string $value` | `static` | Set header (immutable) |
| `withBody()` | `string $body` | `static` | Set body (immutable) |
| `status()` | — | `int` | Get status |
| `headers()` | — | `array` | Get headers |
| `body()` | — | `string` | Get body |
