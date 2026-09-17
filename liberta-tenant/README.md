# liberta-tenant

> Multi-tenant context management for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-tenant
```

## Usage

### Tenant Context

```php
use Liberta\Tenant\TenantContext;

// Set current tenant
TenantContext::setTenantId('company_123');

// Get current tenant
$tenantId = TenantContext::getTenantId();

// Check if in tenant context
if (TenantContext::hasTenant()) {
    // ...
}

// Clear tenant
TenantContext::clear();
```

### Tenant Resolvers

```php
use Liberta\Tenant\HeaderTenantResolver;
use Liberta\Tenant\SubdomainTenantResolver;

// Header-based resolver
$headerResolver = new HeaderTenantResolver('X-Tenant-ID');
$tenantId = $headerResolver->resolve($request);

// Subdomain-based resolver
$subdomainResolver = new SubdomainTenantResolver();
$tenantId = $subdomainResolver->resolve($request);
```

### Middleware Integration

```php
use Liberta\Tenant\TenantContext;
use Liberta\Tenant\HeaderTenantResolver;

class TenantMiddleware
{
    public function __construct(
        private HeaderTenantResolver $resolver
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $tenantId = $this->resolver->resolve($request);

        if ($tenantId === null) {
            return Response::json(['error' => 'Tenant not specified'], 400);
        }

        TenantContext::setTenantId($tenantId);

        return $next($request);
    }
}
```

### Tenant Access Middleware (Security)

Validasi bahwa user yang ter-authentifikasi belong ke tenant yang diklaim.
Mencegah tenant impersonation via `X-Tenant-ID` header.

```php
use Liberta\Tenant\Middleware\TenantAccessMiddleware;

// Wajibkan tenant + validasi ownership
$middleware = new TenantAccessMiddleware();

// Custom header name
$middleware = new TenantAccessMiddleware(header: 'X-Org-ID');

// Tenant optional (tidak wajib)
$middleware = new TenantAccessMiddleware(required: false);
```

**Ownership detection** (otomatis via method reflection):
- Object dengan method `tenants()` → cek `in_array($tenantId, $user->tenants())`
- Object dengan method `tenantId()` → cek `$user->tenantId() === $tenantId`
- Array dengan key `tenant_id` → cek `$user['tenant_id'] === $tenantId`

**Override custom logic:**

Buat subclass untuk logic ownership yang lebih kompleks:

```php
use Liberta\Tenant\Middleware\TenantAccessMiddleware;

class CustomTenantAccess extends TenantAccessMiddleware
{
    protected function userBelongsToTenant(mixed $user, string $tenantId): bool
    {
        // Custom logic: query database, check pivot table, etc.
        return $this->db->table('user_tenants')
            ->where('user_id', '=', $user->id)
            ->where('tenant_id', '=', $tenantId)
            ->exists();
    }
}
```

### Multi-Database Setup

```php
use Liberta\Connection\ConnectionManager;
use Liberta\Tenant\TenantContext;

$manager = new ConnectionManager([
    'company_1' => ['dsn' => 'mysql:host=localhost;dbname=company_1'],
    'company_2' => ['dsn' => 'mysql:host=localhost;dbname=company_2'],
]);

// Get tenant-specific connection
$tenantId = TenantContext::getTenantId();
$pdo = $manager->connection($tenantId);
```

### Service with Tenant

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

## API

### TenantContext

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `setTenantId()` | `string $tenantId` | `void` | Set current tenant |
| `getTenantId()` | — | `string` | Get current tenant |
| `hasTenant()` | — | `bool` | Check if tenant set |
| `clear()` | — | `void` | Clear tenant context |

### TenantResolver Interface

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `resolve()` | `Request $request` | `?string` | Resolve tenant from request |
