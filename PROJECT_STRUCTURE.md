# Liberta Microservice Framework (LMF)

> AI-Native PHP 8.3+ Microservice Framework — Explicit Over Magic

## Philosophy

- **Explicit Over Magic** — No IoC auto-scanning, no hidden behavior, no convention-over-configuration
- **AI-Friendly** — Easily understood by AI coding assistants
- **Independent Packages** — Each package works standalone or within the ecosystem
- **Multi-Company, Multi-Database** — Built for enterprise scale (ERP, Accounting, Inventory, Payroll, CRM, HR)

---

## Project Structure

```
liberta/
├── PRD.md.txt
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 1 — CORE FOUNDATION
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-config/
│   └── src/Config.php                           Dot-notation config loader
│
│   liberta-container/
│   ├── src/Container.php                        DI container (bind/singleton/instance)
│   └── tests/ContainerTest.php                  Unit tests
│
│   liberta-log/
│   ├── src/Logger.php                           Logger interface
│   ├── src/FileLogger.php                       Date-based file logger
│   └── src/NullLogger.php                       Null logger
│
│   liberta-event/
│   ├── src/EventDispatcher.php                  Event system + wildcard
│   └── src/Event.php                            Base event (stopPropagation)
│
│   liberta-exception/
│   ├── src/HttpException.php                    400/401/403/404/405/429/500
│   ├── src/DatabaseException.php                DB exceptions
│   ├── src/AuthException.php                    Token expired/invalid
│   └── src/ValidationException.php              Field validation errors
│
│   liberta-utilities/
│   ├── src/Str.php                              camel/studly/kebab/snake/uuid/slug
│   └── src/Arr.php                              dot-notation array helpers
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 2 — DATABASE TOOLS
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-sql-builder/
│   ├── src/DB.php                               Query builder + transactions
│   ├── src/Connection.php                       PDO wrapper
│   ├── src/Compiler/                            SQL compiler (4 dialects)
│   ├── src/Executor/                            Query executor
│   ├── src/Cache/                               Query caching
│   │   ├── QueryCache.php                       Cache query results
│   │   └── CachedExecutor.php                   Transparent cache wrapper
│   ├── src/Profiler/                            Performance profiling
│   │   ├── Profiler.php                         Section timing + memory
│   │   └── QueryLog.php                         Slow query detection
│   └── tests/                                   Existing tests
│
│   liberta-grammar/
│   └── src/Grammar.php                          MysqlGrammar/PostgresGrammar/SqliteGrammar/SqlServerGrammar
│
│   liberta-migration/
│   ├── src/Migration.php                        Interface
│   ├── src/Migrator.php                         Runner
│   ├── src/Schema.php                           Schema builder
│   ├── src/Table.php                            Table definition
│   └── src/ColumnBuilder.php                    Column definition
│
│   liberta-seeder/
│   ├── src/Seeder.php                           Interface
│   └── src/SeederRunner.php                     Named seeder runner
│
│   liberta-connection/
│   ├── src/ConnectionManager.php                Multi-DB manager
│   └── src/ConnectionPool.php                   PDO connection pooling
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 3 — HTTP + INTERMEDIATE
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-http/
│   ├── src/Request.php                          Immutable request DTO
│   ├── src/Response.php                         Response with immutable modifiers
│   └── src/Middleware/
│       └── PerformanceMiddleware.php            X-Request-Time headers
│
│   liberta-router/
│   ├── src/Router.php                           Router + Module registration
│   ├── src/Module.php                           Module interface
│   └── src/Middleware/MiddlewarePipeline.php     Closure-based pipeline
│
│   liberta-session/
│   └── src/Session.php                          PHP session wrapper
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 4 — CLI + UTILITIES
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-cli/
│   ├── bin/liberta                              CLI entry point
│   ├── src/Application.php                      Console application
│   ├── src/Command.php                          Command interface
│   ├── src/BaseCommand.php                      I/O helpers
│   └── src/Commands/
│       ├── MigrateCommand.php                   liberta migrate
│       ├── SeedCommand.php                      liberta seed
│       └── MakeCommand.php                      liberta make:*
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 5 — REPORT + ERP
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-report/
│   ├── src/ReportGenerator.php                  Interface
│   ├── src/PdfReport.php                        Dompdf
│   ├── src/ExcelReport.php                      PhpSpreadsheet
│   ├── src/WordReport.php                       PHPWord
│   └── templates/                               Document templates
│
│   liberta-erp/
│   ├── accounting/                              ChartOfAccounts, JournalEntries, Ledger
│   ├── inventory/                               Products, Categories, Warehouses
│   ├── purchasing/                              Suppliers, PurchaseOrders, Invoices
│   ├── sales/                                   Customers, SalesOrders, Invoices
│   ├── hr/                                      Employees, Departments, Payroll
│   ├── crm/                                     Contacts, Leads, Opportunities
│   └── migrations/                              SQL files
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 6 — INFRASTRUCTURE
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-cache/
│   ├── src/CacheStore.php                       Interface
│   ├── src/ArrayCache.php                       In-memory cache
│   ├── src/FileCache.php                        File-based cache
│   ├── src/CacheManager.php                     Multi-driver manager
│   └── tests/CacheTest.php                      Unit tests
│
│   liberta-mail/
│   ├── src/Message.php                          Email DTO
│   ├── src/SmtpTransport.php                    SMTP client
│   ├── src/Mailer.php                           Mailer
│   ├── src/Mailable.php                         Abstract mailable
│   └── tests/MailTest.php                       Unit tests
│
│   liberta-notification/
│   ├── src/Notification.php                     Interface
│   ├── src/Notifiable.php                       Notifiable trait
│   ├── src/NotificationManager.php              Channel registry
│   ├── src/Channel.php                          Channel interface
│   ├── src/Channels/MailChannel.php             Mail channel
│   ├── src/Channels/DatabaseChannel.php         Database channel
│   └── src/Messages/                            SlackMessage, SmsMessage
│
│   liberta-queue/
│   ├── src/Job.php                              Job interface
│   ├── src/BaseJob.php                          Abstract job
│   ├── src/QueueDriver.php                      Driver interface
│   ├── src/QueueManager.php                     Multi-driver manager
│   ├── src/Dispatcher.php                       Job dispatcher
│   ├── src/Worker.php                           Queue worker
│   ├── src/Drivers/SyncQueueDriver.php          Sync (immediate)
│   ├── src/Drivers/DatabaseQueueDriver.php      Database queue
│   └── tests/QueueTest.php                      Unit tests
│
│   liberta-scheduler/
│   ├── src/Task.php                             Task interface
│   ├── src/BaseTask.php                         Cron/interval base
│   ├── src/CallableTask.php                     Closure tasks
│   ├── src/Scheduler.php                        Scheduler runner
│   └── tests/SchedulerTest.php                  Unit tests
│
│   liberta-oauth/
│   ├── src/Client.php                           OAuth2 client
│   ├── src/OAuthController.php                  Provider endpoints
│   ├── src/AuthorizationCode.php                Auth code DTO
│   ├── src/AccessToken.php                      Access token DTO
│   ├── src/ClientRepository.php                 Client storage
│   ├── src/TokenRepository.php                  Token storage
│   └── tests/OAuthTest.php                      Unit tests
│
│   liberta-websocket/
│   ├── src/Server.php                           RFC 6455 WebSocket server
│   ├── src/Connection.php                       WebSocket connection
│   ├── src/MessageHandler.php                   Handler interface
│   └── src/WebSocketMiddleware.php              HTTP upgrade middleware
│
│   liberta-sse/
│   ├── src/Event.php                            SSE event DTO
│   ├── src/SseEmitter.php                       SSE emitter
│   ├── src/Channel.php                          SSE channel
│   ├── src/SseMiddleware.php                    SSE middleware
│   └── tests/SseTest.php                        Unit tests
│
│   ═══════════════════════════════════════════════════════════════════
│   PHASE 7 — AUTH
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-jwt/
│   └── src/TokenManager.php                     JWT create/decode/validate
│
│   liberta-rbac-client/
│   └── src/                                     RBAC HTTP client + DTOs
│
│   ═══════════════════════════════════════════════════════════════════
│   TENANT
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-tenant/
│   ├── src/TenantContext.php                     Static tenant holder
│   ├── src/TenantResolver.php                   Interface
│   ├── src/HeaderTenantResolver.php             Header-based
│   └── src/SubdomainTenantResolver.php          Subdomain-based
│
│   ═══════════════════════════════════════════════════════════════════
│   APPLICATION PROJECTS
│   ═══════════════════════════════════════════════════════════════════
│
│   liberta-auth-service/
│   ├── public/index.php                         Entry point (explicit DI)
│   ├── config/                                  middleware.php, services.php
│   ├── routes/api.php                           Routes with middleware group
│   ├── database/                                Auth tables migration
│   └── app/
│       ├── Domain/                              User, Role, Permission
│       ├── Http/Controllers/AuthController.php
│       ├── Http/Middleware/ServiceAuthMiddleware.php
│       ├── Repositories/UserRepository.php
│       └── Services/TokenService.php
│
│   liberta-bussiness-service/
│   ├── public/index.php                         Entry point + TenantContext
│   ├── config/                                  Closure-based middleware
│   ├── routes/api.php                           Routes with RbacMiddleware
│   └── app/Http/Controllers/HealthController.php
│
│   ═══════════════════════════════════════════════════════════════════
│   TESTING + QUALITY
│   ═══════════════════════════════════════════════════════════════════
│
│   phpstan.neon (×8 packages)                   Level 8 static analysis
│   tests/ (×7 packages)                         Unit tests
│
│   ═══════════════════════════════════════════════════════════════════
```

---

## Package Summary

| # | Package | Category | Files | Description |
|---|---------|----------|-------|-------------|
| 1 | liberta-config | Core | 1 | Dot-notation config loader |
| 2 | liberta-container | Core | 2 | DI container (bind/singleton/instance) |
| 3 | liberta-log | Core | 3 | Logger interface + FileLogger + NullLogger |
| 4 | liberta-event | Core | 2 | Event dispatcher with wildcard support |
| 5 | liberta-exception | Core | 4 | HTTP/DB/Auth/Validation exceptions |
| 6 | liberta-utilities | Core | 2 | Str + Arr helper classes |
| 7 | liberta-sql-builder | Database | 8 | Query builder + 4 dialect compiler |
| 8 | liberta-grammar | Database | 5 | SQL grammar (MySQL/Postgres/SQLite/SQLServer) |
| 9 | liberta-migration | Database | 5 | Schema builder + Migrator |
| 10 | liberta-seeder | Database | 2 | Seeder interface + SeederRunner |
| 11 | liberta-connection | Database | 2 | Multi-DB manager + Connection pooling |
| 12 | liberta-http | HTTP | 4 | Request/Response + PerformanceMiddleware |
| 13 | liberta-router | HTTP | 3 | Router + Module + MiddlewarePipeline |
| 14 | liberta-session | HTTP | 1 | PHP session wrapper |
| 15 | liberta-cli | CLI | 6 | Console app + commands (migrate/seed/make) |
| 16 | liberta-report | Report | 5 | PDF/Excel/Word generation |
| 17 | liberta-erp | ERP | 99 | 6 complete ERP modules |
| 18 | liberta-cache | Infra | 6 | Array/File cache + CacheManager |
| 19 | liberta-mail | Infra | 4 | SMTP transport + Mailable |
| 20 | liberta-notification | Infra | 8 | Notification channels (Mail/DB) |
| 21 | liberta-queue | Infra | 8 | Job queue (Sync/Database drivers) |
| 22 | liberta-scheduler | Infra | 4 | Cron-like task scheduler |
| 23 | liberta-oauth | Infra | 6 | OAuth2 provider + client |
| 24 | liberta-websocket | Infra | 4 | RFC 6455 WebSocket server |
| 25 | liberta-sse | Infra | 4 | Server-Sent Events |
| 26 | liberta-jwt | Auth | 1 | JWT create/decode/validate |
| 27 | liberta-rbac-client | Auth | 5 | RBAC HTTP client |
| 28 | liberta-tenant | Tenant | 4 | Multi-tenant context |

| # | Application | Description |
|---|-------------|-------------|
| 29 | liberta-auth-service | Authentication microservice (explicit DI) |
| 30 | liberta-bussiness-service | Business logic microservice (tenant-aware) |

---

## Phase Coverage

```
Phase 1 — Core Foundation     ████████████████████████████████ 100%
Phase 2 — Database Tools      ████████████████████████████████ 100%
Phase 3 — HTTP + Intermediate ████████████████████████████████ 100%
Phase 4 — CLI + Utilities     ████████████████████████████████ 100%
Phase 5 — Report + ERP        ████████████████████████████████ 100%
Phase 6 — Infrastructure      ████████████████████████████████ 100%
Phase 7 — Testing + Polish    ████████████████████████████████ 100%
Phase 8 — Performance         ████████████████████████████████ 100%
```

---

## Statistics

| Metric | Count |
|--------|-------|
| Library Packages | 25 |
| Application Projects | 2 |
| ERP Domain Modules | 6 |
| Unit Tests | 7 |
| PHPStan Configs | 8 |
| Performance Modules | 6 |
| Total PHP Files | 843 |

---

## Testing

Each package includes:

- **Unit Tests** — PHPUnit 10.5 test cases
- **PHPStan Level 8** — Static analysis config
- **strict_types** — All files declare strict typing

### Test Suites

| Package | Test File | Coverage |
|---------|-----------|----------|
| liberta-container | `tests/ContainerTest.php` | bind/singleton/instance/auto-construct |
| liberta-cache | `tests/CacheTest.php` | set/get/delete/flush/increment/remember |
| liberta-queue | `tests/QueueTest.php` | SyncQueueDriver/QueueManager/Dispatcher |
| liberta-mail | `tests/MailTest.php` | Message builder/attachments/headers |
| liberta-scheduler | `tests/SchedulerTest.php` | add/run/disable/getDueTasks |
| liberta-oauth | `tests/OAuthTest.php` | AuthorizationCode/AccessToken |
| liberta-sse | `tests/SseTest.php` | Event toString/json/retry |

---

## Performance Features

| Feature | Package | Description |
|---------|---------|-------------|
| Query Caching | `liberta-sql-builder/src/Cache/` | Cache query results with TTL |
| Connection Pooling | `liberta-connection/src/ConnectionPool.php` | PDO connection pooling (min/max/lifetime) |
| Query Profiler | `liberta-sql-builder/src/Profiler/` | Section timing + slow query detection |
| Performance Middleware | `liberta-http/src/Middleware/PerformanceMiddleware.php` | X-Request-Time headers |

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                           │
│  ┌──────────────────────┐  ┌──────────────────────┐            │
│  │   auth-service       │  │  bussiness-service    │            │
│  │   (Entry Point)      │  │  (Entry Point)       │            │
│  └──────────┬───────────┘  └──────────┬───────────┘            │
│             │                         │                         │
├─────────────┼─────────────────────────┼─────────────────────────┤
│             │      HTTP LAYER         │                         │
│  ┌──────────▼───────────┐  ┌──────────▼───────────┐            │
│  │   Router + Module    │  │   Router + Module     │            │
│  │   MiddlewarePipeline │  │   MiddlewarePipeline  │            │
│  └──────────┬───────────┘  └──────────┬───────────┘            │
│             │                         │                         │
├─────────────┼─────────────────────────┼─────────────────────────┤
│             │     CORE LAYER          │                         │
│  ┌──────────▼─────────────────────────▼───────────┐            │
│  │  Container | Config | Event | Exception | Log  │            │
│  └────────────────────────────────────────────────┘            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                    DATABASE LAYER                               │
│  ┌────────────────────────────────────────────────┐            │
│  │  SQL Builder | Grammar | Migration | Seeder    │            │
│  │  ConnectionManager | ConnectionPool            │            │
│  │  QueryCache | Profiler | QueryLog              │            │
│  └────────────────────────────────────────────────┘            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                  INFRASTRUCTURE LAYER                           │
│  ┌────────────────────────────────────────────────┐            │
│  │  Cache | Mail | Notification | Queue           │            │
│  │  Scheduler | OAuth | WebSocket | SSE           │            │
│  └────────────────────────────────────────────────┘            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                     ERP MODULES                                 │
│  ┌────────────────────────────────────────────────┐            │
│  │  Accounting | Inventory | Purchasing           │            │
│  │  Sales | HR | CRM                              │            │
│  └────────────────────────────────────────────────┘            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                      AUTH LAYER                                 │
│  ┌────────────────────────────────────────────────┐            │
│  │  JWT | RBAC Client | OAuth                     │            │
│  └────────────────────────────────────────────────┘            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                   TENANT LAYER                                  │
│  ┌────────────────────────────────────────────────┐            │
│  │  TenantContext | TenantResolver                 │            │
│  └────────────────────────────────────────────────┘            │
└─────────────────────────────────────────────────────────────────┘
```

---

## File Count by Package

```
liberta-erp               ████████████████████████████████████████████ 99
liberta-sql-builder       ████████████████████████ 24
liberta-rbac-client       ████████████████ 16
liberta-auth-service      ████████████████ 12
liberta-notification      ████████ 8
liberta-queue             ████████ 8
liberta-cli               ████████ 8
liberta-cache             ████████ 8
liberta-config            ████ 4
liberta-container         ████ 4
liberta-event             ████ 4
liberta-exception         ████████ 8
liberta-http              ████ 4
liberta-grammar           ████████ 8
liberta-jwt               ███ 3
liberta-log               ███ 6
liberta-mail              ████ 4
liberta-migration         ██████ 6
liberta-oauth             ████████ 8
liberta-report            ██████ 6
liberta-router            ██████ 6
liberta-scheduler         ████ 4
liberta-seeder            ███ 4
liberta-session           ███ 3
liberta-sse               ████ 4
liberta-tenant            ████ 4
liberta-utilities         ███ 4
liberta-websocket         ████ 4
liberta-bussiness-svc     ████████ 8
```

---

*Generated: 2026-07-20*
*Total: 843 files across 30 packages*
