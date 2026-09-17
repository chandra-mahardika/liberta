# liberta-auth-service

> Authentication microservice with explicit dependency injection.

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
use Liberta\Jwt\TokenManager;
use Liberta\Auth\{UserRepository, TokenService, AuthController};
use Liberta\Auth\ServiceAuthMiddleware;

// Explicit DI wiring
$container = new Container();
$container->instance('db', DB::getInstance());
$container->instance('jwt', new TokenManager());
$container->instance('users', new UserRepository($container->resolve('db')));
$container->instance('tokens', new TokenService(
    $container->resolve('jwt'),
    $container->resolve('users'),
    $_ENV['JWT_SECRET']
));
$container->instance('auth', new AuthController(
    $container->resolve('tokens'),
    $container->resolve('users')
));

// Routes
$router->post('/login', [$container->resolve('auth'), 'login']);
$router->post('/register', [$container->resolve('auth'), 'register']);
$router->get('/me', [$container->resolve('auth'), 'me'])
    ->middleware(new ServiceAuthMiddleware($container->resolve('jwt')));
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/login` | Login and get JWT token |
| POST | `/register` | Register new user |
| GET | `/me` | Get current user (requires token) |
| POST | `/refresh` | Refresh JWT token |
| POST | `/logout` | Logout (invalidate token) |

### Login Request

```json
POST /login
{
    "email": "user@example.com",
    "password": "password"
}

Response:
{
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### Register Request

```json
POST /register
{
    "name": "Chandra Mahardika",
    "email": "chandra.libertania@gmail.com",
    "password": "secret"
}

Response:
{
    "id": 1,
    "name": "Chandra Mahardika",
    "email": "chandra.libertania@gmail.com"
}
```

### Get Current User

```
GET /me
Authorization: Bearer eyJ...

Response:
{
    "id": 1,
    "name": "Chandra Mahardika",
    "email": "chandra.libertania@gmail.com",
    "roles": ["user"],
    "permissions": ["users.read"]
}
```

## Configuration

### Environment Variables

Copy `.env.example` to `.env` and generate secure secrets:

```bash
cp .env.example .env
php -r "echo 'JWT_SECRET=' . bin2hex(random_bytes(32));" >> .env
php -r "echo 'AUTH_KEY=' . bin2hex(random_bytes(32));" >> .env
```

**IMPORTANT:** `JWT_SECRET` and `AUTH_KEY` are **required** — the app will throw `RuntimeException` if not set.

### config/services.php

```php
return [
    'auth' => [
        'url' => Env::getString('AUTH_URL', 'http://auth-service.internal'),
        'key' => Env::required('AUTH_KEY'),  // Required, no default
    ],
    'jwt' => [
        'secret'    => Env::required('JWT_SECRET'),  // Required, no default
        'algorithm' => Env::getString('JWT_ALGORITHM', 'HS256'),
        'expiry'    => Env::getInt('JWT_EXPIRY', 3600),
    ],
];
```

### Security Notes

- **Service Auth:** Uses `hash_equals()` for constant-time comparison (prevents timing attacks)
- **Error Handler:** Internal exception details are logged to `error_log()`, only generic messages returned to client
- **JWT Secret:** Must be set via environment variable, no hardcoded fallback

### Database Tables

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_roles (
    user_id BIGINT UNSIGNED,
    role_id BIGINT UNSIGNED,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

## Project Structure

```
liberta-auth-service/
├── public/
│   └── index.php                 Entry point
├── config/
│   ├── middleware.php             Middleware config
│   └── services.php               Service config
├── routes/
│   └── api.php                    Route definitions
├── database/
│   └── 001_create_auth_tables.php Migration
└── app/
    ├── Domain/
    │   ├── User.php               User domain model
    │   ├── Role.php               Role domain model
    │   └── Permission.php         Permission domain model
    ├── Http/
    │   ├── Controllers/
    │   │   └── AuthController.php Auth controller
    │   └── Middleware/
    │       └── ServiceAuthMiddleware.php Auth middleware
    ├── Repositories/
    │   └── UserRepository.php     User repository
    └── Services/
        └── TokenService.php       JWT token service
```
