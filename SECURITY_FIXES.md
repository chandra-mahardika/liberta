# Security Fixes — Liberta Microservice Framework

Daftar lengkap perbaikan keamanan yang telah diterapkan ke dalam framework.
Audit dilakukan dan perbaikan diimplementasikan pada 20 Juli 2026.

---

## Daftar Isi

- [Ringkasan](#ringkasan)
- [New Middlewares](#new-middlewares)
  - [SecurityHeadersMiddleware](#1-securityheadersmiddleware)
  - [RateLimitMiddleware](#2-ratelimitmiddleware)
  - [CorsMiddleware](#3-corsmiddleware)
  - [ValidationMiddleware + Validator](#4-validationmiddleware--validator)
  - [TenantAccessMiddleware](#5-tenantaccessmiddleware)
- [Bug Fixes](#bug-fixes)
  - [Timing Attack — ServiceAuthMiddleware](#6-timing-attack--serviceauthmiddleware)
  - [Error Leakage — Exception Handler](#7-error-leakage--exception-handler)
  - [Session Cookie Security](#8-session-cookie-security)
  - [SQL Escape — SqlHelper::wrapValue()](#9-sql-escape--sqlhelperwrapvalue)
  - [Bearer Prefix — RbacMiddleware](#10-bearer-prefix--rbacmiddleware)
  - [JWT getSubject() Tanpa Verifikasi](#11-jwt-getsubject-tanpa-verifikasi)
  - [OAuth State Parameter](#12-oauth-state-parameter)
  - [OAuth cURL Error Handling](#13-oauth-curl-error-handling)
  - [Secret Management](#14-secret-management)

---

## Ringkasan

| Kategori | Jumlah |
|---|---|
| Middleware baru | 5 |
| Bug fixes | 9 |
| Total file diubah | 17 |
| Severity: Critical | 7 |
| Severity: High | 4 |
| Severity: Medium | 3 |

---

## New Middlewares

### 1. SecurityHeadersMiddleware

**Package:** `liberta-http`
**File:** `src/Middleware/SecurityHeadersMiddleware.php`
**Severity:** HIGH

Menambahkan security headers ke setiap response HTTP.

| Header | Nilai Default | Fungsi |
|---|---|---|
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` | Paksa HTTPS selama 1 tahun |
| `X-Content-Type-Options` | `nosniff` | Cegah MIME-type sniffing |
| `X-Frame-Options` | `DENY` | Cegah clickjacking |
| `X-XSS-Protection` | `1; mode=block` | Aktifkan XSS filter browser |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Kontrol informasi referrer |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=()` | Batasi browser features |
| `Cache-Control` | `no-store, no-cache, must-revalidate, private` | Cegah caching sensitif |

**Cara pakai:**

```php
use Liberta\Http\Middleware\SecurityHeadersMiddleware;

// Default headers
$middleware = new SecurityHeadersMiddleware();

// Custom overrides
$middleware = new SecurityHeadersMiddleware([
    'X-Frame-Options' => 'SAMEORIGIN',
    'Strict-Transport-Security' => 'max-age=63072000',
]);
```

---

### 2. RateLimitMiddleware

**Package:** `liberta-http`
**File:** `src/Middleware/RateLimitMiddleware.php`
**Severity:** HIGH

Rate limiting berbasis file per IP + path. Mencegah brute force dan DDoS ringan.

**Fitur:**
- Tracking per IP + path
- Configurable max attempts dan window time
- Response headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- `Retry-After` header saat limit tercapai
- Auto-cleanup expired entries

**Cara pakai:**

```php
use Liberta\Http\Middleware\RateLimitMiddleware;

// Default: 60 request per menit
$middleware = new RateLimitMiddleware();

// Custom: 10 request per 60 detik
$middleware = new RateLimitMiddleware(
    maxAttempts: 10,
    windowSeconds: 60,
    storagePath: '/tmp/liberta_rate_limit'
);
```

**Response saat limit tercapai:**

```json
{
    "success": false,
    "message": "Too Many Requests"
}
```

Headers: `Retry-After: 45`, `X-RateLimit-Limit: 10`, `X-RateLimit-Remaining: 0`

---

### 3. CorsMiddleware

**Package:** `liberta-http`
**File:** `src/Middleware/CorsMiddleware.php`
**Severity:** HIGH

CORS middleware dengan support preflight, origin whitelist, dan credentials.

**Fitur:**
- Preflight `OPTIONS` handling otomatis
- Origin whitelist validation
- Configurable allowed methods, headers, credentials
- `Access-Control-Expose-Headers` untuk rate limit headers

**Cara pakai:**

```php
use Liberta\Http\Middleware\CorsMiddleware;

// Allow all origins (development)
$middleware = new CorsMiddleware();

// Restrict ke specific origins (production)
$middleware = new CorsMiddleware(
    allowedOrigins: ['https://app.example.com', 'https://admin.example.com'],
    allowedMethods: ['GET', 'POST', 'PUT', 'DELETE'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Tenant-ID'],
    allowCredentials: true,
    maxAge: 86400
);
```

---

### 4. ValidationMiddleware + Validator

**Package:** `liberta-http`
**File:** `src/Middleware/ValidationMiddleware.php`, `src/Validation/Validator.php`
**Severity:** CRITICAL

Request input validation — mengatasi tidak adanya input validation di framework.

**Rules yang tersedia:**

| Rule | Parameter | Deskripsi |
|---|---|---|
| `required` | — | Wajib diisi |
| `string` | — | Harus string |
| `integer` | — | Harus angka bulat |
| `float` | — | Harus angka |
| `email` | — | Harus email valid |
| `min` | `N` | Minimal N karakter/angka/item |
| `max` | `N` | Maksimal N karakter/angka/item |
| `between` | `N,M` | Antara N dan M |
| `in` | `a,b,c` | Harus salah satu dari list |
| `alpha` | — | Hanya huruf |
| `alphaNum` | — | Huruf dan angka |
| `alphaDash` | — | Huruf, angka, dash, underscore |
| `date` | — | Tanggal valid |
| `url` | — | URL valid |
| `uuid` | — | UUID valid |
| `regex` | `pattern` | Regex pattern |
| `confirmed` | — | Cocok dengan `{field}_confirmation` |
| `array` | — | Harus array |

**Cara pakai:**

```php
use Liberta\Http\Middleware\ValidationMiddleware;

// Validasi request body
$validateCustomer = new ValidationMiddleware(
    rules: [
        'name'  => 'required|string|max:255',
        'email' => 'required|email',
        'age'   => 'integer|min:0|max:150',
    ]
);

// Validasi query params
$validateSearch = new ValidationMiddleware(
    rules: [
        'q'    => 'required|string|min:3',
        'page' => 'integer|min:1',
    ],
    source: 'query'
);

// Custom error messages
$validateLogin = new ValidationMiddleware(
    rules: [
        'email'    => 'required|email',
        'password' => 'required|min:8',
    ],
    messages: [
        'email.required'    => 'Email wajib diisi',
        'email.email'       => 'Format email tidak valid',
        'password.required' => 'Password wajib diisi',
        'password.min'      => 'Password minimal 8 karakter',
    ]
);
```

**Error response (422):**

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "email": ["Format email tidak valid"],
        "password": ["Password minimal 8 karakter"]
    }
}
```

**Standalone Validator:**

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
```

---

### 5. TenantAccessMiddleware

**Package:** `liberta-tenant`
**File:** `src/Middleware/TenantAccessMiddleware.php`
**Severity:** HIGH

Memvalidasi bahwa user yang ter-authentifikasi memang belong ke tenant yang diklaim. Mencegah tenant impersonation via header.

**Fitur:**
- Extract tenant ID dari header
- Validasi user ownership (via `tenants()`, `tenantId()`, atau `tenant_id`)
- Support required/optional mode
- Override `userBelongsToTenant()` untuk custom logic

**Cara pakai:**

```php
use Liberta\Tenant\Middleware\TenantAccessMiddleware;

// Wajibkan tenant ID + validasi ownership
$middleware = new TenantAccessMiddleware();

// Tenant ID optional
$middleware = new TenantAccessMiddleware(required: false);

// Custom header
$middleware = new TenantAccessMiddleware(header: 'X-Org-ID');
```

---

## Bug Fixes

### 6. Timing Attack — ServiceAuthMiddleware

**Package:** `liberta-auth-service`
**File:** `app/Http/Middleware/ServiceAuthMiddleware.php:20`
**Severity:** HIGH

**Sebelum:**
```php
if ($key !== $this->serviceKey) {
```

**Sesudah:**
```php
if ($key === null || !hash_equals($this->serviceKey, $key)) {
```

**Alasan:** Operator `!==` melakukan perbandingan yang berbeda durasi tergantung posisi karakter yang salah. Attacker bisa mengekstrak service key satu karakter pada satu waktu melalui timing difference. `hash_equals()` melakukan constant-time comparison.

---

### 7. Error Leakage — Exception Handler

**Package:** `liberta-auth-service`, `liberta-bussiness-service`
**File:** `public/index.php`
**Severity:** CRITICAL

**Sebelum:**
```php
} catch (Throwable $e) {
    Response::error($e->getMessage(), $e->getCode() ?: 500)->send();
}
```

**Sesudah:**
```php
} catch (Throwable $e) {
    $statusCode = $e->getCode() ?: 500;
    $message = match (true) {
        $e instanceof ValidationException => $e->getMessage(),
        $e instanceof NotFoundException    => 'Not Found',
        $e instanceof BadRequestException => 'Bad Request',
        $e instanceof UnauthorizedException => 'Unauthorized',
        $e instanceof ForbiddenException  => 'Forbidden',
        $e instanceof TooManyRequestsException => 'Too Many Requests',
        default => 'Internal Server Error',
    };
    error_log(sprintf('[%s] %s in %s:%d', date('Y-m-d H:i:s'), $e->getMessage(), $e->getFile(), $e->getLine()));
    Response::error($message, $statusCode)->send();
}
```

**Alasan:** Sebelumnya, exception message (termasuk nama class internal, DB driver, file paths) terekspos ke API consumer. Sekarang hanya generic message yang dikirim ke client, detail internal di-log ke server.

---

### 8. Session Cookie Security

**Package:** `liberta-session`
**File:** `src/Session.php:15-16`
**Severity:** MEDIUM

**Perubahan:**
- `secure` flag default: `false` → `true`
- Tambah `sameSite` parameter default: `'Lax'`

**Alasan:** Cookie tanpa `secure=true` bisa dikirim via HTTP (tidak terenkripsi). Tanpa `SameSite`, rentan terhadap CSRF attacks.

---

### 9. SQL Escape — SqlHelper::wrapValue()

**Package:** `liberta-sql-builder`
**File:** `src/Traits/SqlHelper.php:9-11`
**Severity:** MEDIUM

**Sebelum:**
```php
return is_int($value) ? (string) $value : "'" . addslashes($value) . "'";
```

**Sesudah:**
```php
if (is_int($value))   return (string) $value;
if (is_float($value)) return (string) $value;
if ($value === null)  return 'NULL';
if (is_bool($value))  return $value ? 'TRUE' : 'FALSE';
return '\'' . addcslashes((string) $value, "\000\n\r\\'\"\032") . '\'';
```

**Alasan:** `addslashes()` tidak escape karakter null byte (`\000`) dan beberapa karakter berbahaya lainnya. `addcslashes()` dengan explicit character list lebih aman. Namun sebaiknya gunakan parameterized queries (prepared statements) untuk user input.

---

### 10. Bearer Prefix — RbacMiddleware

**Package:** `liberta-bussiness-service`
**File:** `app/Http/Middleware/RbacMiddleware.php:18`
**Severity:** MEDIUM

**Sebelum:**
```php
$token = $request->headers()['Authorization'] ?? null;
$user = $this->auth->authenticate($token);
```

**Sesudah:**
```php
$token = $request->headers()['Authorization'] ?? null;
if ($token !== null) {
    $token = preg_replace('/^Bearer\s+/i', '', $token);
}
$user = $this->auth->authenticate($token);
```

**Alasan:** Header `Authorization` berisi `Bearer <token>`, bukan token murni. Tanpa stripping, token tidak akan valid di auth service.

---

### 11. JWT getSubject() Tanpa Verifikasi

**Package:** `liberta-jwt`
**File:** `src/TokenManager.php:59-70`
**Severity:** MEDIUM

**Sebelum:**
```php
public function getSubject(string $token): ?string
{
    $parts = explode('.', $token);
    $payload = json_decode(base64_decode($parts[1]), true);
    return $payload['sub'] ?? null;
}
```

**Sesudah:**
```php
public function getSubject(string $token): ?string
{
    try {
        $payload = $this->decode($token);
        return $payload['sub'] ?? null;
    } catch (\Throwable) {
        return null;
    }
}

/** @deprecated Gunakan getSubject() yang validasi signature */
public function getSubjectUnsafe(string $token): ?string { ... }
```

**Alasan:** Versi lama membaca payload JWT tanpa memverifikasi signature. Ini bisa dieksploitasi untuk mengklaim subject apapun. Versi baru memanggil `decode()` yang validasi signature menggunakan `firebase/php-jwt`.

---

### 12. OAuth State Parameter

**Package:** `liberta-oauth`
**File:** `src/OAuthController.php:18-44`
**Severity:** CRITICAL

**Perubahan:**
- Validasi `state` parameter (minimal 16 karakter)
- `urlencode()` pada state value di redirect URL

**Alasan:** Tanpa validasi state, OAuth flow rentan CSRF attack — attacker bisa mengaitkan authorization code milik mereka ke session victim. State harus unpredictable dan divalidasi di callback.

---

### 13. OAuth cURL Error Handling

**Package:** `liberta-oauth`
**File:** `src/Client.php:80-118`
**Severity:** MEDIUM

**Perubahan:**
- Tambah `CURLOPT_TIMEOUT` (30s) dan `CURLOPT_CONNECTTIMEOUT` (10s)
- Check `curl_errno()` setelah `curl_exec()`
- Validate response JSON dan HTTP status code
- Throw `\RuntimeException` saat error

**Alasan:** Sebelumnya, cURL errors diabaikan — `json_decode(null)` menghasilkan `null` yang menyebabkan error downstream tanpa pesan jelas.

---

### 14. Secret Management

**Package:** `liberta-auth-service`, `liberta-bussiness-service`
**File:** `.env`, `.env.example`, `config/services.php`
**Severity:** CRITICAL

**Perubahan:**
- `.env`: Placeholder weak secrets diganti dengan instruksi generate
- `config/services.php`: `Env::getString()` dengan default weak secret → `Env::required()` yang throw RuntimeException jika tidak diset
- `public/index.php`: Hapus fallback `'CHANGE_ME'` untuk JWT secret
- `.env.example`: Tambah warning comments dan command generate

**Sebelum:**
```php
'jwt' => ['secret' => Env::getString('JWT_SECRET', 'CHANGE_ME_TO_A_SECURE_RANDOM_STRING')]
```

**Sesudah:**
```php
'jwt' => ['secret' => Env::required('JWT_SECRET')]
```

**Generate secret:**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

---

## Setup Lengkap

### Middleware Pipeline Example

Berikut contoh penerapan semua security middleware di `config/middleware.php`:

```php
<?php

use Liberta\Http\Middleware\SecurityHeadersMiddleware;
use Liberta\Http\Middleware\CorsMiddleware;
use Liberta\Http\Middleware\RateLimitMiddleware;
use Liberta\Http\Middleware\ValidationMiddleware;
use Liberta\Tenant\Middleware\TenantAccessMiddleware;
use App\Http\Middleware\RbacMiddleware;

$config = require __DIR__ . '/services.php';

$authClient = new \Liberta\Rbac\Client\HttpAuthClient(
    $config['auth']['url'],
    $config['auth']['key']
);

return [
    'security_headers' => fn() => new SecurityHeadersMiddleware(),

    'cors' => fn() => new CorsMiddleware(
        allowedOrigins: ['https://app.example.com'],
        allowCredentials: true,
    ),

    'rate_limit' => fn() => new RateLimitMiddleware(
        maxAttempts: 60,
        windowSeconds: 60,
    ),

    'rate_limit_strict' => fn() => new RateLimitMiddleware(
        maxAttempts: 5,
        windowSeconds: 300,
    ),

    'validate_customer' => fn() => new ValidationMiddleware(
        rules: [
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|min:10|max:20',
        ]
    ),

    'validate_login' => fn() => new ValidationMiddleware(
        rules: [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]
    ),

    'tenant_access' => fn() => new TenantAccessMiddleware(),
    'rbac' => fn() => new RbacMiddleware($authClient),
    'service_auth' => fn() => new \App\Http\Middleware\ServiceAuthMiddleware($config['auth']['key']),
];
```

### Route Definition Example

```php
<?php

// routes/api.php

$router->post('/api/customers', [CustomerController::class, 'store'])
    ->middleware([
        'security_headers',
        'cors',
        'rate_limit',
        'validate_customer',
        'rbac',
    ]);

$router->post('/api/auth/login', [AuthController::class, 'login'])
    ->middleware([
        'security_headers',
        'rate_limit_strict',
        'validate_login',
    ]);

$router->get('/api/products', [ProductController::class, 'index'])
    ->middleware([
        'security_headers',
        'cors',
        'rate_limit',
        'rbac',
        'tenant_access',
    ]);
```

---

## Checklist untuk Production

- [ ] Generate JWT secret baru: `php -r "echo bin2hex(random_bytes(32));"`
- [ ] Generate AUTH_KEY baru dan pastikan match antar service
- [ ] Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`
- [ ] Set `DB_PASSWORD` dengan password kuat
- [ ] Enable HTTPS di reverse proxy / load balancer
- [ ] Register semua middleware di `config/middleware.php`
- [ ] Apply middleware ke semua routes
- [ ] Konfigurasi `CorsMiddleware` dengan production origins
- [ ] Sesuaikan `RateLimitMiddleware` limits sesuai kebutuhan
- [ ] Pastikan `Session::secure = true` untuk session-based auth
