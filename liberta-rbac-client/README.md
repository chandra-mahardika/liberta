# liberta-rbac-client

> RBAC (Role-Based Access Control) HTTP client for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-rbac-client
```

## Usage

### Authentication Client

```php
use Liberta\RbacClient\Client\HttpAuthClient;

$client = new HttpAuthClient(
    baseUrl: 'https://auth.example.com/api',
    timeout: 10
);

// Login
$tokens = $client->login('user@example.com', 'password');
$accessToken = $tokens['access_token'];

// Get user
$user = $client->getUser($accessToken);

// Check permission
$hasPermission = $client->checkPermission($accessToken, 'users.create');

// Logout
$client->logout($accessToken);
```

### User DTO

```php
use Liberta\RbacClient\DTO\User;

$user = new User(
    id: 1,
    name: 'Chandra',
    email: 'chandra.libertania@gmail.com',
    roles: ['admin', 'user'],
    permissions: ['users.create', 'users.read', 'users.update']
);

// Check roles
$user->hasRole('admin');     // true
$user->hasRole('superadmin'); // false

// Get array
$array = $user->toArray();
```

### Middleware Integration

```php
use Liberta\RbacClient\Client\HttpAuthClient;

// In your middleware
class AuthMiddleware
{
    public function __construct(
        private HttpAuthClient $client
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $token = $request->bearerToken();

        if ($token === null) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        try {
            $user = $this->client->getUser($token);
            $request->setUser($user);
        } catch (\Throwable $e) {
            return Response::json(['error' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
```

## API

### HttpAuthClient

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `login()` | `string $email, string $password` | `array` | Login and get tokens |
| `getUser()` | `string $accessToken` | `User` | Get user info |
| `checkPermission()` | `string $accessToken, string $permission` | `bool` | Check permission |
| `logout()` | `string $accessToken` | `void` | Logout |

### User DTO

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `hasRole()` | `string $role` | `bool` | Check if has role |
| `toArray()` | — | `array` | Convert to array |
