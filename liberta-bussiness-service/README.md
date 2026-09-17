# liberta-bussiness-service

> Business logic microservice with multi-tenant support.

## Installation

```bash
composer install
```

## Usage

### Entry Point

```php
// public/index.php
use Liberta\Container\Container;
use Liberta\SqlBuilder\DB;
use Liberta\Connection\ConnectionManager;
use Liberta\Tenant\TenantContext;
use Liberta\Tenant\HeaderTenantResolver;
use Liberta\Http\Request;
use Liberta\Http\Response;

// Tenant resolver
$resolver = new HeaderTenantResolver('X-Tenant-ID');
$tenantId = $resolver->resolve(Request::fromGlobals());

if ($tenantId !== null) {
    TenantContext::setTenantId($tenantId);
}

// Multi-DB connection
$manager = new ConnectionManager([
    'tenant_1' => ['dsn' => 'mysql:host=localhost;dbname=tenant_1'],
    'tenant_2' => ['dsn' => 'mysql:host=localhost;dbname=tenant_2'],
]);

// Routes
$router->get('/health', function () {
    return Response::json(['status' => 'ok']);
});
```

### Configuration

#### Environment Variables

Copy `.env.example` to `.env` and generate secure keys:

```bash
cp .env.example .env
php -r "echo 'AUTH_KEY=' . bin2hex(random_bytes(32));" >> .env
```

**IMPORTANT:** `AUTH_KEY` must match the `AUTH_KEY` in `liberta-auth-service`.

#### config/services.php

```php
return [
    'auth' => [
        'url' => Env::getString('AUTH_URL', 'http://auth-service.internal'),
        'key' => Env::required('AUTH_KEY'),  // Required, must match auth-service
    ],
    'connections' => [
        'primary' => [
            'driver'   => Env::getString('DB_DRIVER', 'mysql'),
            'host'     => Env::getString('DB_HOST', 'localhost'),
            'port'     => Env::getInt('DB_PORT', 3306),
            'charset'  => Env::getString('DB_CHARSET', 'utf8mb4'),
            'database' => Env::getString('DB_DATABASE', 'business'),
            'username' => Env::getString('DB_USERNAME', 'root'),
            'password' => Env::getString('DB_PASSWORD', ''),
        ],
    ],
];
```

#### config/middleware.php

```php
<?php

use App\Http\Middleware\RbacMiddleware;
use Liberta\Http\Middleware\SecurityHeadersMiddleware;
use Liberta\Http\Middleware\CorsMiddleware;
use Liberta\Http\Middleware\RateLimitMiddleware;
use Liberta\Tenant\Middleware\TenantAccessMiddleware;

$config = require __DIR__ . '/services.php';

$authClient = new \Liberta\Rbac\Client\HttpAuthClient(
    $config['auth']['url'],
    $config['auth']['key']
);

return [
    'security_headers' => fn() => new SecurityHeadersMiddleware(),
    'cors' => fn() => new CorsMiddleware(['https://app.example.com']),
    'rate_limit' => fn() => new RateLimitMiddleware(maxAttempts: 60, windowSeconds: 60),
    'tenant_access' => fn() => new TenantAccessMiddleware(),
    'rbac' => fn() => new RbacMiddleware($authClient),
];
```

### Security Notes

- **Bearer Token:** RbacMiddleware automatically strips `Bearer ` prefix from Authorization header
- **Tenant Validation:** TenantAccessMiddleware validates user belongs to claimed tenant
- **Error Handler:** Internal details logged, only generic messages returned to client

### Routes

```php
// routes/api.php
$router->prefix('api', function ($router) {
    $router->get('/products', [ProductController::class, 'index'])
        ->middleware(['security_headers', 'cors', 'rate_limit', 'rbac', 'tenant_access']);

    $router->post('/products', [ProductController::class, 'store'])
        ->middleware(['security_headers', 'cors', 'rate_limit', 'rbac', 'tenant_access']);

    $router->get('/health', [HealthController::class, 'index']);
});
```

### Tenant-Aware Service

```php
class ProductService
{
    public function __construct(
        private DB $db
    ) {}

    public function getProducts(): array
    {
        $tenantId = TenantContext::getTenantId();

        return $this->db->table('products')
            ->where('tenant_id', '=', $tenantId)
            ->get();
    }
}
```

## Project Structure

```
liberta-bussiness-service/
├── public/
│   └── index.php                 Entry point
├── config/
│   ├── middleware.php             Closure-based middleware
│   └── services.php               Service config
├── routes/
│   └── api.php                    Route definitions
└── app/
    └── Http/
        ├── Controllers/
        │   └── HealthController.php Health check
        └── Middleware/
            └── RbacMiddleware.php RBAC middleware
```
