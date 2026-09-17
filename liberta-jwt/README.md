# liberta-jwt

> JWT token creation, decoding, and validation.

## Installation

```bash
composer require chandra/liberta-jwt
```

## Usage

### Creating Tokens

```php
use Liberta\Jwt\TokenManager;

$jwt = new TokenManager(
    secret: 'your-secret-key',
    algorithm: 'HS256',
    defaultTtl: 3600
);

// Create token with subject and custom claims
$token = $jwt->create('user-123', ['role' => 'admin'], 3600);

// The token includes: iss, sub, iat, exp + custom claims
```

### Decoding Tokens

```php
// Decode and validate token (throws InvalidTokenException / TokenExpiredException)
$payload = $jwt->decode($token);

$subject = $payload['sub'];  // 'user-123'
$role    = $payload['role']; // 'admin'
$exp     = $payload['exp'];
```

### Getting Subject (with validation)

```php
// Safe: validates signature before extracting subject
$subject = $jwt->getSubject($token);

// Unsafe: reads payload WITHOUT signature verification (deprecated)
$subject = $jwt->getSubjectUnsafe($token);
```

### Checking Expiration

```php
// Check without throwing
if ($jwt->isExpired($token)) {
    throw new AuthException('Token expired');
}
```

### Validating Tokens

```php
// Check if token is valid
$payload = $jwt->decode($token, $secret);

if ($payload === null) {
    throw new AuthException('Invalid token');
}

// Check expiration
if (isset($payload['exp']) && $payload['exp'] < time()) {
    throw new AuthException('Token expired');
}
```

### Using with Auth

```php
use Liberta\Jwt\TokenManager;
use Liberta\Exception\AuthException;

class TokenService
{
    public function __construct(
        private TokenManager $jwt,
        private string $secret
    ) {}

    public function createToken(array $user): string
    {
        return $this->jwt->create(
            ['user_id' => $user['id'], 'email' => $user['email']],
            $this->secret,
            3600
        );
    }

    public function validateToken(string $token): array
    {
        $payload = $this->jwt->decode($token, $this->secret);

        if ($payload === null) {
            throw AuthException::tokenInvalid();
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw AuthException::tokenExpired();
        }

        return $payload;
    }
}
```

## API

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `create()` | `string $subject, array $claims = [], ?int $ttl = null` | `string` | Create JWT token |
| `decode()` | `string $token` | `array` | Decode and validate token (throws on invalid/expired) |
| `getSubject()` | `string $token` | `?string` | Get subject with signature validation |
| `getSubjectUnsafe()` | `string $token` | `?string` | Get subject without validation (deprecated) |
| `isExpired()` | `string $token` | `bool` | Check if token is expired |
