# Configuration

> Configuration management for Libertà applications with `.env` support.

---

## 1. Overview

Libertà uses a layered configuration system:

```
┌─────────────────────────────────────────────────────────────┐
│                  CONFIGURATION LAYERS                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Environment Variables (.env)                            │
│     └── Highest priority, sensitive data                    │
│                                                             │
│  2. Config Files (config/*.php)                             │
│     └── Application configuration                           │
│                                                             │
│  3. Defaults (in code)                                      │
│     └── Fallback values                                     │
│                                                             │
│  Priority: .env > config/*.php > defaults                   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Environment Variables

### 2.1 File Structure

```
your-project/
├── .env                  ← Actual configuration (gitignored)
├── .env.example          ← Template (committed to git)
├── .gitignore            ← Excludes .env
└── config/
    └── services.php      ← Uses Env class
```

### 2.2 .env File

```env
# Application
APP_NAME=my-app
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

# Database
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=my_database
DB_USERNAME=root
DB_PASSWORD=secret_password

# JWT
JWT_SECRET=your-256-bit-secret-key
JWT_ALGORITHM=HS256
JWT_EXPIRY=3600

# Auth Service
AUTH_URL=http://auth-service:8080
AUTH_KEY=service-secret-key
```

### 2.3 .env.example

```env
# Application
APP_NAME=my-app
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

# Database
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=my_database
DB_USERNAME=root
DB_PASSWORD=              # ← Leave empty in example

# JWT
JWT_SECRET=               # ← Leave empty in example
JWT_ALGORITHM=HS256
JWT_EXPIRY=3600

# Auth Service
AUTH_URL=http://auth-service:8080
AUTH_KEY=                 # ← Leave empty in example
```

### 2.4 .gitignore

```gitignore
# Environment
.env
.env.local
.env.production
.env.staging

# Dependencies
/vendor/

# IDE
.vscode/
.idea/

# Logs
*.log
/logs/

# Cache
/cache/
/storage/
```

---

## 3. Env Loader Class

### 3.1 Basic Usage

```php
use Liberta\Config\Env;

// Load .env file
Env::load(__DIR__ . '/../.env');

// Get values
$host = Env::getString('DB_HOST', 'localhost');
$port = Env::getInt('DB_PORT', 3306);
$debug = Env::getBool('APP_DEBUG', false);

// Required (throws exception if missing)
$secret = Env::required('JWT_SECRET');
```

### 3.2 API

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `load()` | `string $path` | `void` | Load .env file |
| `get()` | `string $key, mixed $default = null` | `mixed` | Get value |
| `getString()` | `string $key, string $default = ''` | `string` | Get as string |
| `getInt()` | `string $key, int $default = 0` | `int` | Get as integer |
| `getBool()` | `string $key, bool $default = false` | `bool` | Get as boolean |
| `required()` | `string $key` | `mixed` | Get required value |

### 3.3 Type Conversion

```php
// Boolean conversion
.Env::getBool('APP_DEBUG');
// "true" → true
// "false" → false
// "1" → true
// "0" → false

// Integer conversion
.Env::getInt('DB_PORT');
// "3306" → 3306

// Null conversion
.Env::get('DATABASE_URL');
// "null" → null
```

---

## 4. Config Files

### 4.1 services.php

```php
<?php

use Liberta\Config\Env;

// Load .env file
Env::load(__DIR__ . '/../.env');

return [
    'database' => [
        'driver'   => Env::getString('DB_DRIVER', 'mysql'),
        'host'     => Env::getString('DB_HOST', 'localhost'),
        'port'     => Env::getInt('DB_PORT', 3306),
        'charset'  => Env::getString('DB_CHARSET', 'utf8mb4'),
        'database' => Env::getString('DB_DATABASE', 'my_database'),
        'username' => Env::getString('DB_USERNAME', 'root'),
        'password' => Env::getString('DB_PASSWORD', ''),
    ],

    'jwt' => [
        'secret'    => Env::getString('JWT_SECRET', 'CHANGE_ME'),
        'algorithm' => Env::getString('JWT_ALGORITHM', 'HS256'),
        'expiry'    => Env::getInt('JWT_EXPIRY', 3600),
    ],

    'auth' => [
        'url' => Env::getString('AUTH_URL', 'http://auth-service:8080'),
        'key' => Env::getString('AUTH_KEY', ''),
    ],
];
```

### 4.2 middleware.php

```php
<?php

use Liberta\Config\Env;

return [
    'rate_limit' => Env::getInt('RATE_LIMIT', 60),
    'cors_origins' => Env::getString('CORS_ORIGINS', '*'),
];
```

---

## 5. Application Projects

### 5.1 liberta-auth-service

```
liberta-auth-service/
├── .env                  ← Actual config
├── .env.example          ← Template
├── .gitignore            ← Excludes .env
├── config/
│   ├── services.php      ← Uses Env class
│   └── middleware.php    ← Middleware config
└── ...
```

#### .env

```env
# Application
APP_NAME=liberta-auth-service
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

# Database
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=liberta_auth
DB_USERNAME=root
DB_PASSWORD=

# JWT
JWT_SECRET=CHANGE_ME_TO_A_SECURE_RANDOM_STRING
JWT_ALGORITHM=HS256
JWT_EXPIRY=3600

# Auth Service
AUTH_URL=http://auth-service.internal
AUTH_KEY=SERVICE_SECRET_KEY
```

### 5.2 liberta-bussiness-service

```
liberta-bussiness-service/
├── .env                  ← Actual config
├── .env.example          ← Template
├── .gitignore            ← Excludes .env
├── config/
│   ├── services.php      ← Uses Env class
│   └── middleware.php    ← Middleware config
└── ...
```

#### .env

```env
# Application
APP_NAME=liberta-business-service
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8081

# Primary Database
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=business
DB_USERNAME=root
DB_PASSWORD=

# Auth Service
AUTH_URL=http://auth-service.internal
AUTH_KEY=SERVICE_SECRET_KEY

# Tenant
TENANT_HEADER=X-Tenant-ID
```

---

## 6. Usage Patterns

### 6.1 Entry Point (public/index.php)

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Liberta\Config\Env;

// Load environment variables
Env::load(__DIR__ . '/../.env');

// Now config files can use Env::get()
$config = require __DIR__ . '/../config/services.php';
```

### 6.2 In Service Classes

```php
use Liberta\Config\Env;

class TokenService
{
    public function __construct(
        private string $secret
    ) {}

    public static function create(): self
    {
        return new self(
            Env::required('JWT_SECRET')
        );
    }
}
```

### 6.3 In Controllers

```php
use Liberta\Config\Env;

class AuthController
{
    public function login(Request $request): Response
    {
        $debug = Env::getBool('APP_DEBUG');

        if ($debug) {
            // Debug mode
        }

        // ...
    }
}
```

---

## 7. Environment-Specific Config

### 7.1 Multiple .env Files

```
your-project/
├── .env                  ← Default (local development)
├── .env.local            ← Local overrides
├── .env.staging          ← Staging environment
└── .env.production       ← Production environment
```

### 7.2 Loading Specific Environment

```php
use Liberta\Config\Env;

// Load default
Env::load(__DIR__ . '/../.env');

// Override with environment-specific
$env = $_ENV['APP_ENV'] ?? 'local';
$envFile = __DIR__ . '/../.env.' . $env;

if (file_exists($envFile)) {
    Env::load($envFile);
}
```

### 7.3 Docker

```dockerfile
# Dockerfile
COPY .env.production .env
```

```yaml
# docker-compose.yml
services:
  app:
    env_file:
      - .env
      - .env.local
```

---

## 8. Security Best Practices

### 8.1 Never Commit Secrets

```gitignore
# .gitignore
.env
.env.local
.env.production
```

### 8.2 Use .env.example as Template

```bash
# .env.example (committed)
DB_PASSWORD=
JWT_SECRET=
AUTH_KEY=

# .env (gitignored, actual values)
DB_PASSWORD=secret_password
JWT_SECRET=your-256-bit-secret-key
AUTH_KEY=service-secret-key
```

### 8.3 Required Variables

```php
use Liberta\Config\Env;

// Throws RuntimeException if missing
$secret = Env::required('JWT_SECRET');
```

### 8.4 Validation

```php
use Liberta\Config\Env;

// Validate required vars on startup
$required = ['DB_HOST', 'DB_DATABASE', 'JWT_SECRET'];

foreach ($required as $key) {
    Env::required($key);
}
```

---

## 9. Comparison with Other Frameworks

| Framework | Config Method | Env Support |
|-----------|---------------|-------------|
| Laravel | `.env` + `config()` | ✅ Built-in |
| Symfony | `.env` + `%env()%` | ✅ Built-in |
| CodeIgniter | `.env` + `$this->config` | ✅ Manual |
| **Libertà** | `.env` + `Env::get()` | ✅ Custom |

---

## 10. Summary

```
┌─────────────────────────────────────────────────────────────┐
│                 CONFIGURATION SYSTEM                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  .env File                                                  │
│  ├── DB_HOST=localhost                                       │
│  ├── DB_PASSWORD=secret                                     │
│  └── JWT_SECRET=xxx                                         │
│           │                                                 │
│           ▼                                                 │
│  Env::load()                                                │
│  └── Parses .env into $_ENV                                 │
│           │                                                 │
│           ▼                                                 │
│  config/services.php                                        │
│  └── Uses Env::getString('DB_HOST', 'localhost')            │
│           │                                                 │
│           ▼                                                 │
│  Application                                                │
│  └── Gets configuration values                              │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

| Feature | Description |
|---------|-------------|
| **Layered Config** | .env → config/*.php → defaults |
| **Type Safety** | getString, getInt, getBool |
| **Required Variables** | Env::required() throws if missing |
| **Git Safe** | .env excluded, .env.example committed |
| **Docker Ready** | Use env_file in docker-compose |
| **Environment-Specific** | .env.local, .env.production |

---

*Generated: 2026-07-20*
*Libertà Microservice Framework — Configuration Management*
