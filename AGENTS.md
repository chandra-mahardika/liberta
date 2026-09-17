# AGENTS.md

> File ini dibaca otomatis oleh opencode untuk memahami project.
> Update file ini saat ada perubahan signifikan.

---

## Project Overview

**Liberta Microservice Framework (LMF)** — AI-Native PHP 8.3+ Microservice Framework

Filosofi utama: **Explicit Over Magic** — tidak ada auto-scanning, tidak ada auto-binding, tidak ada hidden lifecycle. Seluruh dependency harus terlihat di source code.

Setiap package bisa digunakan secara standalone atau bersama-sama dalam satu framework.

**Package count:** 25 library packages + 2 application projects

---

## Tech Stack

- PHP 8.3+
- PDO (MySQL, PostgreSQL, SQLite, SQLServer)
- PHPUnit 10.5
- PHPStan Level 8
- Composer

---

## Project Structure

```
liberta/
├── ARCHITECTURE.md          ← Detailed architecture docs
├── PROJECT_STRUCTURE.md     ← Full structure with file listing
├── PRD.md                   ← Product requirements
├── CONFIG.md                ← Configuration guide (.env, layered config)
├── SECURITY_FIXES.md        ← Security audit & fixes (20 Juli 2026)
├── AGENTS.md                ← This file
│
├── [Core Packages]
│   ├── liberta-config/      Dot-notation config loader
│   ├── liberta-container/   DI container (bind/singleton/instance)
│   ├── liberta-log/         Logger (FileLogger, NullLogger)
│   ├── liberta-event/       Event dispatcher + wildcard
│   ├── liberta-exception/   HTTP/DB/Auth/Validation exceptions
│   └── liberta-utilities/   Str + Arr helpers
│
├── [Database Packages]
│   ├── liberta-sql-builder/ Query builder + 4 dialect compiler
│   ├── liberta-grammar/     SQL grammar per dialect
│   ├── liberta-migration/   Schema builder + Migrator
│   ├── liberta-seeder/      Seeder interface + SeederRunner
│   └── liberta-connection/  Multi-DB manager + Connection pooling
│
├── [HTTP Packages]
│   ├── liberta-http/        Request/Response DTOs + Middlewares
│   ├── liberta-router/      Router + Module + MiddlewarePipeline
│   └── liberta-session/     PHP session wrapper
│
├── [Infrastructure Packages]
│   ├── liberta-cache/       Array/File cache + CacheManager
│   ├── liberta-mail/        SMTP transport + Mailable
│   ├── liberta-notification/ Notification channels (Mail/DB)
│   ├── liberta-queue/       Job queue (Sync/Database drivers)
│   ├── liberta-scheduler/   Cron-like task scheduler
│   ├── liberta-oauth/       OAuth2 provider + client
│   ├── liberta-websocket/   RFC 6455 WebSocket server
│   └── liberta-sse/         Server-Sent Events
│
├── [Auth Packages]
│   ├── liberta-jwt/         JWT create/decode/validate
│   └── liberta-rbac-client/ RBAC HTTP client
│
├── [Other Packages]
│   ├── liberta-cli/         Console app + commands
│   ├── liberta-report/      PDF/Excel/Word generation
│   ├── liberta-erp/         6 ERP modules (accounting, inventory, etc.)
│   └── liberta-tenant/      Multi-tenant context
│
└── [Application Projects]
    ├── liberta-auth-service/     Auth microservice (explicit DI)
    └── liberta-bussiness-service/ Business microservice (tenant-aware)
```

---

## Commands

### Run Tests (PHPUnit)

```bash
# Per-package
cd liberta-container && vendor/bin/phpunit
cd liberta-sql-builder && vendor/bin/phpunit
cd liberta-cache && vendor/bin/phpunit
cd liberta-queue && vendor/bin/phpunit
cd liberta-mail && vendor/bin/phpunit
cd liberta-scheduler && vendor/bin/phpunit
cd liberta-oauth && vendor/bin/phpunit
cd liberta-sse && vendor/bin/phpunit
```

### Static Analysis (PHPStan Level 8)

```bash
cd liberta-container && vendor/bin/phpstan analyse
cd liberta-cache && vendor/bin/phpstan analyse
cd liberta-queue && vendor/bin/phpstan analyse
cd liberta-mail && vendor/bin/phpstan analyse
cd liberta-scheduler && vendor/bin/phpstan analyse
cd liberta-oauth && vendor/bin/phpstan analyse
cd liberta-websocket && vendor/bin/phpstan analyse
cd liberta-sse && vendor/bin/phpstan analyse
```

### Install Dependencies

```bash
cd <package-name> && composer install
```

---

## Conventions

### Naming

| Type | Convention | Example |
|------|-----------|---------|
| Class | PascalCase | `UserRepository`, `TokenService` |
| Method | camelCase | `findById()`, `getToken()` |
| Variable | camelCase | `$userRepo`, `$tokenManager` |
| File | PascalCase.php | `UserRepository.php` |
| Migration | Create*Table | `CreateUsersTable` |
| Constant | UPPER_SNAKE | `MAX_RETRY` |

### Folder Structure per Package

```
liberta-<name>/
├── src/
│   ├── <ClassName>.php
│   └── ...
├── tests/
│   └── <Name>Test.php
├── composer.json
├── phpstan.neon          (if applicable)
└── README.md
```

### Code Style

- `declare(strict_types=1);` di semua file PHP
- Tidak ada trailing whitespace
- Tidak ada komentar kecuali diminta
- Explicit dependency injection — tidak ada service locator
- Constructor-based DI — parameter di constructor = dependency
- Readonly properties untuk immutable DTOs
- PHP 8.3 features: enums, readonly, fibers, named args

### Dependency Injection Pattern

```php
// ✅ Correct — explicit
$container->instance('db', $db);
$repo = new UserRepository($db);

// ❌ Wrong — hidden/magic
$repo = $container->make(UserRepository::class); // auto-resolve
```

### Middleware Pattern

```php
// Closure-based pipeline
$pipeline->pipe(function ($request, $next) {
    // before
    $response = $next($request);
    // after
    return $response;
});
```

### Multi-Database Pattern

```php
// ConnectionManager handles multiple DB connections
$manager = new ConnectionManager($config);
$pdo = $manager->connection('tenant_1'); // switches DB per tenant
```

---

## Security Notes

- Service auth uses timing-safe comparison (`hash_equals`)
- Rate limiting per IP + path (file-based)
- Security headers on all responses (HSTS, X-Frame-Options, etc.)
- CORS configurable per route
- JWT verification on all protected endpoints
- OAuth state parameter required
- See `SECURITY_FIXES.md` for full audit (20 Juli 2026)

---

## Key Design Decisions

1. **No auto-scanning** — Everything registered explicitly in code
2. **No IoC container auto-wiring** — Manual binding only
3. **PDO directly** — No ORM abstraction layer
4. **Standalone packages** — Use only what you need
5. **AI-friendly** — Structure and naming optimized for AI understanding
6. **Multi-tenant** — Each company has its own DB connection via ConnectionManager
7. **Microservice-ready** — Auth, Business, and other services are separate

---

## Current Status

All 8 phases completed:
- Phase 1: Core Foundation ✅
- Phase 2: Database Tools ✅
- Phase 3: HTTP + Intermediate ✅
- Phase 4: CLI + Utilities ✅
- Phase 5: Report + ERP ✅
- Phase 6: Infrastructure ✅
- Phase 7: Testing + Polish ✅
- Phase 8: Performance ✅

**Total:** 843 PHP files, 25 library packages, 2 apps, 6 ERP modules, 7 test suites, 8 PHPStan configs

---

## Tips for AI Assistant

1. Baca `ARCHITECTURE.md` untuk detail arsitektur lengkap
2. Baca `PROJECT_STRUCTURE.md` untuk listing semua file
3. Baca `PRD.md` untuk product requirements
4. Baca `CONFIG.md` untuk configuration guide
5. Baca `SECURITY_FIXES.md` untuk security audit
6. Setiap package punya `README.md` dengan API docs
7. Ikuti naming convention yang sudah ada
8. Selalu pakai `declare(strict_types=1)`
9. Jangan tambah dependency baru tanpa pertimbangan
10. Test harus pass sebelum commit
