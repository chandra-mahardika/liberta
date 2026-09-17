# Liberta Microservice Framework

AI-Native PHP Microservice Framework — **Explicit Over Magic**

Liberta adalah framework microservice berbasis native PHP yang ringan, transparan, dan modular. Dibangun dengan prinsip utama: seluruh alur aplikasi harus bisa dipahami hanya dengan membaca source code — tanpa auto-scanning, tanpa auto-binding, tanpa hidden lifecycle.

> "Framework should help developers think, not replace how developers think."

---

## Daftar Isi

- [Filosofi](#filosofi)
- [Keunggulan](#keunggulan)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Struktur Project](#struktur-project)
- [Instalasi](#instalasi)
- [Quick Start](#quick-start)
- [Alur Request](#alur-request)
- [Pengujian & Static Analysis](#pengujian--static-analysis)
- [Aplikasi Microservice](#aplikasi-microservice)
- [Keamanan](#keamanan)
- [Lisensi](#lisensi)

---

## Filosofi

### Explicit Over Magic

```php
<?php
// ❌ Laravel (magic): bagaimana container resolve UserController?
Route::get('/users', [UserController::class, 'index']);

// ✅ Liberta (eksplisit): semua dependency terlihat
$repo       = new UserRepository($db);
$controller = new UserController($repo);
$router->get('/users', [$controller, 'index']);
```

- Tidak ada auto-scanning folder
- Tidak ada auto-binding / auto-wiring IoC
- Tidak ada service locator
- Dependensi diinjeksi lewat constructor dan terlihat di source code

### AI-Friendly

- Struktur folder konsisten di semua package
- Naming convention ketat (`XxxController`, `XxxService`, `XxxRepository`)
- Lifecycle linear & mudah ditrace
- Sedikit abstraksi — memakai native PHP (PDO, enum, readonly, closure)
- Setiap package punya `README.md` berisi dokumentasi API

### PHP Native First

Memanfaatkan kemampuan asli PHP: **PDO** untuk database, **curl** untuk HTTP/mail, **enum/readonly/anonymous function** untuk domain logic. Tidak membuat abstraksi kalau PHP sudah punya solusi yang baik.

### Package Driven & Minimal Core

Framework bukan kumpulan fitur, melainkan kumpulan **package independen**. Gunakan hanya yang dibutuhkan — baik standalone maupun bersama-sama.

---

## Keunggulan

| Fitur | Keterangan |
|-------|-----------|
| Microservice-ready | Auth, Business, dll. sebagai service terpisah |
| Multi-database | PDO: MySQL, PostgreSQL, SQLite, SQLServer |
| Query Builder | SQL-first, multi-dialect compiler, transaction, join, aggregate |
| Multi-tenant | Perusahaan punya koneksi DB sendiri via `ConnectionManager` |
| Real-time | WebSocket (RFC 6455) + Server-Sent Events (SSE) |
| Infrastructure | Cache, Queue, Scheduler, Mail, Notification, OAuth |
| Reporting | PDF (Dompdf), Excel (PhpSpreadsheet), Word (PHPWord) |
| ERP Ready | 6 modul: Accounting, Inventory, Purchasing, Sales, HR, CRM |
| Kualitas | PHPUnit 10.5 + PHPStan Level 8 |

---

## Persyaratan Sistem

- **PHP** ^8.2 (direkomendasikan 8.3+ untuk fitur `readonly`, enum, type system lengkap)
- **Composer** 2.x
- **Extensions:** PDO (`pdo_mysql` / `pdo_pgsql` / `pdo_sqlite` / `pdo_sqlsrv` sesuai kebutuhan)
- **Opsional:** `curl` (mail & rbac client), `dom` (PDF), `zip` (Excel/Word)

---

## Struktur Project

```
liberta/
├── liberta-config/          Dot-notation config loader + Env
├── liberta-container/       DI container (bind/singleton/instance)
├── liberta-log/             Logger (FileLogger, NullLogger)
├── liberta-event/           Event dispatcher + wildcard listener
├── liberta-exception/       HTTP/DB/Auth/Validation exceptions
├── liberta-utilities/       Str + Arr helpers
│
├── liberta-sql-builder/     Query builder + 4 dialect compiler + cache/profiler
├── liberta-grammar/         SQL grammar per dialect (MySQL/Postgres/SQLite/SQLServer)
├── liberta-migration/       Schema builder + migrator
├── liberta-seeder/          Seeder interface + seeder runner
├── liberta-connection/      Multi-connection manager + connection pooling
│
├── liberta-http/            Request/Response DTO + middleware (CORS, rate limit, security)
├── liberta-router/          Router + Module + MiddlewarePipeline
├── liberta-session/         PHP session wrapper
│
├── liberta-cache/           Cache (Array/File/Tagged) + CacheManager
├── liberta-mail/            SMTP transport + Mailable
├── liberta-notification/    Channel notification (Mail/DB)
├── liberta-queue/           Job queue (Sync/Database) + worker
├── liberta-scheduler/       Cron-like task scheduler
├── liberta-oauth/           OAuth2 provider + client
├── liberta-websocket/       WebSocket server (RFC 6455)
├── liberta-sse/             Server-Sent Events
│
├── liberta-jwt/             JWT create/decode/validate
├── liberta-rbac-client/     RBAC HTTP client + DTO
├── liberta-tenant/          Multi-tenant context (header/subdomain)
│
├── liberta-cli/             Console app + commands (migrate/seed/make)
├── liberta-report/          PDF/Excel/Word report generator
├── liberta-erp/             6 modul ERP lengkap (99 file)
│
├── liberta-auth-service/    Aplikasi microservice #1 (auth & RBAC)
└── liberta-bussiness-service/ Aplikasi microservice #2 (tenant-aware)
```

Statistik: **25 library packages + 2 aplikasi**, 843 file PHP, 7 suite pengujian, 8 konfigurasi PHPStan.

---

## Instalasi

### 1. Eksplorasi & Development dalam Monorepo

Setiap package adalah library Composer standalone dengan `vendor/` sendiri. Untuk mengembangkan/menjalankan test satu package:

```bash
# masuk ke package yang mau dikerjakan
cd liberta-sql-builder
composer install

# coba pakai package tersebut
cd liberta-auth-service
composer install
```

### 2. Memakai Package di Project Sendiri

Karena ini monorepo, daftarkan package yang dibutuhkan sebagai **path repository** di `composer.json` project kamu. Nama Composer tiap package adalah `chandra/liberta-*` dengan namespace `Liberta\*`.

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../liberta-router",
            "options": {
                "versions": {
                    "chandra/liberta-router": "1.0.0"
                }
            }
        }
    ],
    "require": {
        "php": "^8.2",
        "chandra/liberta-router": "^1.0"
    }
}
```

```bash
composer require chandra/liberta-router
```

Contoh kombinasinya sesuai kebutuhan:

| Kebutuhan | Package |
|-----------|---------|
| Query + cache + log (project PHP native) | `liberta-sql-builder`, `liberta-cache`, `liberta-log` |
| HTTP & routing | `liberta-http`, `liberta-router` |
| Background job | `liberta-queue`, `liberta-scheduler` |
| Email & notifikasi | `liberta-mail`, `liberta-notification` |
| Real-time | `liberta-websocket`, `liberta-sse` |
| Auth | `liberta-jwt`, `liberta-oauth`, `liberta-rbac-client` |
| Multi-tenant | `liberta-tenant`, `liberta-connection` |

### 3. Menjalankan Aplikasi Microservice

Aplikasi `liberta-auth-service` dan `liberta-bussiness-service` membutuhkan package lokal sebagai dependency. Tambahkan **repositories** ke `composer.json` aplikasi lalu ganti nama dependency `liberta/*` → `chandra/liberta-*`:

```json
{
    "require": {
        "php": "^8.2",
        "chandra/liberta-router": "^1.0",
        "chandra/liberta-http": "^1.0",
        "chandra/liberta-sql-builder": "^1.0"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../liberta-router",
            "options": { "versions": { "chandra/liberta-router": "1.0.0" } }
        },
        {
            "type": "path",
            "url": "../liberta-http",
            "options": { "versions": { "chandra/liberta-http": "1.0.0" } }
        },
        {
            "type": "path",
            "url": "../liberta-sql-builder",
            "options": { "versions": { "chandra/liberta-sql-builder": "1.0.0" } }
        }
    ]
}
```

Langkah:

```bash
cd liberta-auth-service
composer install

# siapkan environment
cp .env.example .env
# isi secret: php -r "echo bin2hex(random_bytes(32));"

# jalankan web server bawaan PHP
php -S localhost:8080 -t public
```

> `firebase/php-jwt` (Auth Service) diambil otomatis dari Packagist.

---

## Quick Start

Contoh mikroservice sederhana dengan pola yang sama persis dengan aplikasi bawaan:

```php
<?php
// public/index.php
// 1. buat object secara eksplisit
$router = new Liberta\Router\Router();
$router->get('/users', [UserController::class, 'index']);

$route = $router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if (!$route) {
    Liberta\Http\Response::error('Not Found', 404)->send();
    exit;
}

$request = (new Liberta\Http\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    getallheaders(),
    $_GET,
    $_POST
))->withParams($route['params']);

// 2. wiring dependency (terlihat semua)
$db     = new Liberta\Sql\DB($config['database']);
$users  = new UserRepository($db);
$c      = new UserController($users);

// 3. jalankan middleware pipeline
$pipeline = new Liberta\Router\Middleware\MiddlewarePipeline(
    $route['middlewares'],
    $config['middlewares']
);

$response = $pipeline->handle($request, function ($request) use ($c, $route) {
    [$class, $method] = $route['handler'];
    return $c->{$method}($request);
});

$response->send();
```

Controller:

```php
<?php
class UserController
{
    public function __construct(protected UserRepository $users) {}

    public function index(Liberta\Http\Request $request): Liberta\Http\Response
    {
        return Liberta\Http\Response::json([
            'users' => $this->users->all(),
        ]);
    }
}
```

Navigasi alur di atas: **Request → Router → Middleware Pipeline → Controller → Repository → DB → Response**. Tidak ada langkah tersembunyi.

---

## Alur Request

```
HTTP Request
    ↓
Router (match route + parse params)
    ↓
Middleware Pipeline (auth, permission, logging, ...)
    ↓
Controller   → validasi request
    ↓
Service      → business logic & orchestration
    ↓
Repository   → query database saja
    ↓
Database (PDO)
    ↓
Response
```

- Controller **tidak boleh** berisi business logic
- Service adalah tempat seluruh business rule
- Repository hanya menangani query/CRUD/transaction
- Dependency selalu satu arah (tidak ada circular dependency)

---

## Pengujian & Static Analysis

```bash
# Unit test per package
cd liberta-container && vendor/bin/phpunit
cd liberta-sql-builder && vendor/bin/phpunit
cd liberta-cache       && vendor/bin/phpunit
cd liberta-queue       && vendor/bin/phpunit
cd liberta-mail        && vendor/bin/phpunit
cd liberta-scheduler   && vendor/bin/phpunit
cd liberta-oauth       && vendor/bin/phpunit
cd liberta-sse         && vendor/bin/phpunit

# Static analysis PHPStan Level 8
cd liberta-container && vendor/bin/phpstan analyse
cd liberta-sql-builder && vendor/bin/phpstan analyse
cd liberta-cache       && vendor/bin/phpstan analyse
cd liberta-queue       && vendor/bin/phpstan analyse
cd liberta-mail        && vendor/bin/phpstan analyse
cd liberta-scheduler   && vendor/bin/phpstan analyse
cd liberta-oauth       && vendor/bin/phpstan analyse
cd liberta-sse         && vendor/bin/phpstan analyse
```

CLI helpers (package `liberta-cli`):

```bash
php bin/liberta migrate     # jalankan migration
php bin/liberta seed        # jalankan seeder
php bin/liberta make:xxx    # scaffolding baru
```

---

## Aplikasi Microservice

### `liberta-auth-service`

Central auth & RBAC service.

- Endpoint: `POST /validate` (validasi JWT → kembalikan data user + roles + permissions)
- Middleware: `ServiceAuthMiddleware` (komparasi aman via `hash_equals`)
- Stack: `liberta-router`, `liberta-http`, `liberta-sql-builder`, `firebase/php-jwt`
- File: `app/Domain`, `app/Repositories/UserRepository.php`, `app/Services/TokenService.php`, `app/Http/Controllers/AuthController.php`

### `liberta-bussiness-service`

Business logic service **tenant-aware**.

- Multi-tenant: `ConnectionManager` + `TenantContext::resolve()` dari header `X-Tenant-ID`
- Middleware: `RbacMiddleware` (mengecek permission via RBAC client)
- Stack: `liberta-router`, `liberta-http`, `liberta-sql-builder`, `liberta-connection`, `liberta-tenant`, `liberta-rbac-client`

---

## Keamanan

- Service auth memakai **timing-safe comparison** (`hash_equals`)
- **Rate limiting** per IP + path (file-based)
- **Security headers** di semua response (HSTS, X-Frame-Options, dll.)
- CORS configurable per route
- JWT verification di seluruh protected endpoint
- OAuth `state` parameter wajib diisi
- `.env` tidak pernah di-commit ke repository (gunakan `.env.example` sebagai template)

---

## Lisensi

MIT License — Copyright (c) 2026 Chandra Liberta