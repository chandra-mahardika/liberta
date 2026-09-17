# liberta-exception

> Structured exception hierarchy for HTTP, Database, Auth, and Validation errors.

## Installation

```bash
composer require chandra/liberta-exception
```

## Usage

### HTTP Exceptions

```php
use Liberta\Exception\HttpException;

throw new HttpException(404, 'User not found');

// Specific HTTP errors
throw new HttpException(400, 'Bad request');
throw new HttpException(401, 'Unauthorized');
throw new HttpException(403, 'Forbidden');
throw new HttpException(404, 'Not found');
throw new HttpException(405, 'Method not allowed');
throw new HttpException(429, 'Too many requests');
throw new HttpException(500, 'Internal server error');
```

### Database Exceptions

```php
use Liberta\Exception\DatabaseException;

throw new DatabaseException('Connection failed', 0, $pdoException);
throw new DatabaseException('Query syntax error');
```

### Auth Exceptions

```php
use Liberta\Exception\AuthException;

throw AuthException::tokenExpired();
throw AuthException::tokenInvalid();
throw AuthException::insufficientPermissions('admin');
```

### Validation Exceptions

```php
use Liberta\Exception\ValidationException;

$exception = new ValidationException([
    'email' => ['Email is required', 'Email is invalid'],
    'password' => ['Password must be at least 8 characters'],
]);

// Get errors
$errors = $exception->getErrors(); // ['email' => [...], 'password' => [...]]
$message = $exception->getMessage(); // First error message
```

## Exception Hierarchy

```
Exception
├── HttpException (400, 401, 403, 404, 405, 429, 500)
├── DatabaseException
├── AuthException
└── ValidationException
```

## API

### HttpException

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `getStatusCode()` | — | `int` | Get HTTP status code |

### AuthException

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `tokenExpired()` | — | `static` | Create token expired error |
| `tokenInvalid()` | — | `static` | Create token invalid error |
| `insufficientPermissions()` | `string $permission` | `static` | Create permission error |

### ValidationException

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `getErrors()` | — | `array` | Get all field errors |
| `errors()` | — | `array` | Alias for getErrors() |
