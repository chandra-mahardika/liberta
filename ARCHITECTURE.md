# Liberta Architecture

> AI-Friendly, Standalone, PHP Native Microservice Framework

---

## 1. Design Principles

### 1.1 Explicit Over Magic

```
❌ Laravel:       Route::get('/users', [UserController::class, 'index']);
                 // How does the container resolve this? Hidden magic.

✅ Liberta:       $router->get('/users', [$controller, 'index']);
                 // $controller is created explicitly, no hidden resolution.
```

**Prinsip:**
- Tidak ada auto-scanning
- Tidak ada auto-binding
- Tidak ada hidden lifecycle
- Seluruh dependency harus terlihat di source code

### 1.2 AI-Friendly

```
┌─────────────────────────────────────────────────────────────┐
│                    AI-FRIENDLY DESIGN                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Struktur Folder Konsisten                               │
│     src/           → Source code                            │
│     tests/         → Unit tests                             │
│     config/        → Configuration                          │
│     routes/        → Route definitions                      │
│                                                             │
│  2. Naming Convention Ketat                                 │
│     UserRepository      → Repository untuk User             │
│     AuthController      → Controller untuk Auth             │
│     TokenService        → Service untuk Token               │
│     CreateUsersTable    → Migration untuk users table       │
│                                                             │
│  3. Dependency Eksplisit                                    │
│     $container->instance('db', $db);                        │
│     $repo = new UserRepository($db);                        │
│     // AI langsung paham: UserRepository butuh $db          │
│                                                             │
│  4. Lifecycle Linear                                        │
│     Request → Router → Middleware → Controller → Response    │
│     // AI bisa trace alur tanpa debugger                    │
│                                                             │
│  5. Sedikit Abstraksi                                       │
│     PDO, Enum, Readonly Property, Anonymous Function        │
│     // Menggunakan native PHP, bukan framework abstraction  │
│                                                             │
│  6. Dokumentasi Lengkap                                     │
│     README.md di setiap package                             │
│     API tables, code examples, usage scenarios              │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

**AI Goals:**
- AI dapat memahami source code tanpa konfigurasi tambahan
- AI dapat menghasilkan module baru secara konsisten
- AI dapat melakukan refactor dengan risiko minimal

### 1.3 Standalone Packages

```
┌─────────────────────────────────────────────────────────────┐
│                  STANDALONE PACKAGES                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Setiap package bisa digunakan secara independen:           │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   config    │  │   cache     │  │    mail     │        │
│  │  (standalone)│  │ (standalone)│  │ (standalone)│        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │ sql-builder │  │   queue     │  │    jwt      │        │
│  │ (standalone)│  │ (standalone)│  │ (standalone)│        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   router    │  │   report    │  │  scheduler  │        │
│  │ (standalone)│  │ (standalone)│  │ (standalone)│        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  Tidak perlu install seluruh framework!                     │
│  Pakai hanya yang dibutuhkan.                               │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 1.4 PHP Native First

```php
// ✅ Menggunakan PDO (bukan abstraction layer)
$pdo = new PDO('mysql:host=localhost', 'root', '');
$stmt = $pdo->query('SELECT * FROM users');

// ✅ Menggunakan Enum (bukan constant)
enum Status: string {
    case Active = 'active';
    case Inactive = 'inactive';
}

// ✅ Menggunakan Readonly Property
class User {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}
}

// ✅ Menggunakan Anonymous Function
$router->get('/users', function ($request) {
    return Response::json(['users' => []]);
});
```

---

## 2. Package Independence

### 2.1 Dependency Matrix

```
┌─────────────────────────────────────────────────────────────┐
│                  PACKAGE DEPENDENCIES                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  NO DEPENDENCIES (Can use alone):                           │
│  ├── liberta-config                                         │
│  ├── liberta-container                                      │
│  ├── liberta-log                                            │
│  ├── liberta-event                                          │
│  ├── liberta-exception                                      │
│  ├── liberta-utilities                                      │
│  ├── liberta-http                                           │
│  ├── liberta-session                                        │
│  ├── liberta-cli                                            │
│  ├── liberta-cache                                          │
│  ├── liberta-scheduler                                      │
│  ├── liberta-websocket                                      │
│  ├── liberta-sse                                            │
│  └── liberta-jwt                                            │
│                                                             │
│  OPTIONAL DEPENDENCIES (Can use alone or with dependencies):│
│  ├── liberta-sql-builder     → None (PDO only)              │
│  ├── liberta-grammar         → None                         │
│  ├── liberta-migration       → sql-builder                  │
│  ├── liberta-seeder          → sql-builder                  │
│  ├── liberta-connection      → None (PDO only)              │
│  ├── liberta-mail            → None (curl only)             │
│  ├── liberta-notification    → mail + sql-builder           │
│  ├── liberta-queue           → sql-builder (for DB driver)  │
│  ├── liberta-oauth           → jwt + sql-builder            │
│  ├── liberta-report          → None (dompdf, etc.)          │
│  ├── liberta-rbac-client     → None (curl only)             │
│  └── liberta-tenant          → None                         │
│                                                             │
│  FRAMEWORK PACKAGES (Use with other packages):              │
│  ├── liberta-router          → http                         │
│  └── liberta-erp             → sql-builder + migration      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Usage Scenarios

```
┌─────────────────────────────────────────────────────────────┐
│                  USAGE SCENARIOS                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  SCENARIO 1: PHP Native Project (No Framework)              │
│  ─────────────────────────────────────────────              │
│  // Just use sql-builder + cache                            │
│  composer require chandra/liberta-sql-builder               │
│  composer require chandra/liberta-cache                     │
│                                                             │
│  $db = new DB($pdo);                                        │
│  $cache = new ArrayCache();                                 │
│                                                             │
│  SCENARIO 2: Add Features Incrementally                     │
│  ─────────────────────────────────────────                  │
│  // Start with http + router                                │
│  composer require chandra/liberta-http                      │
│  composer require chandra/liberta-router                    │
│                                                             │
│  // Add queue later                                         │
│  composer require chandra/liberta-queue                     │
│                                                             │
│  // Add mail later                                          │
│  composer require chandra/liberta-mail                      │
│                                                             │
│  SCENARIO 3: Full Libertà Framework                         │
│  ──────────────────────────────────────                     │
│  // Use all packages together                               │
│  composer require chandra/liberta-router                    │
│  composer require chandra/liberta-container                 │
│  composer require chandra/liberta-sql-builder               │
│  // ... etc                                                 │
│                                                             │
│  SCENARIO 4: Microservice Architecture                      │
│  ───────────────────────────────────────                    │
│  // Auth Service: jwt + rbac-client + container             │
│  // User Service: sql-builder + router                      │
│  // Order Service: queue + cache + sql-builder              │
│  // Notification Service: mail + notification + queue       │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. AI Architecture

### 3.1 How AI Understands the Code

```php
// ═══════════════════════════════════════════════════════════
// AI CAN EASILY UNDERSTAND THIS CODE:
// ═══════════════════════════════════════════════════════════

// 1. Entry Point - AI sees all dependencies
$container = new Container();
$container->instance('db', DB::getInstance());
$container->instance('jwt', new TokenManager());
$container->instance('users', new UserRepository($db));

// 2. AI knows:
//    - Container needs DB, JWT, UserRepository
//    - UserRepository needs DB
//    - TokenService needs JWT + UserRepository
//    - All dependencies are explicit

// 3. AI can:
//    - Add new dependencies easily
//    - Refactor without breaking changes
//    - Generate new modules consistently
//    - Debug issues quickly
```

### 3.2 AI Code Generation

```
┌─────────────────────────────────────────────────────────────┐
│                  AI CODE GENERATION                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  INPUT (User Request):                                      │
│  "Create a Product module with CRUD operations"             │
│                                                             │
│  AI OUTPUT (Consistent Pattern):                            │
│                                                             │
│  1. ProductController.php                                   │
│     - index() → GET /products                               │
│     - show()  → GET /products/{id}                          │
│     - store() → POST /products                              │
│     - update()→ PUT /products/{id}                          │
│     - destroy()→ DELETE /products/{id}                      │
│                                                             │
│  2. ProductService.php                                      │
│     - getAll()                                              │
│     - getById()                                             │
│     - create()                                              │
│     - update()                                              │
│     - delete()                                              │
│                                                             │
│  3. ProductRepository.php                                   │
│     - Uses sql-builder pattern                              │
│     - Explicit DB dependency                                │
│                                                             │
│  4. ProductMigration.php                                    │
│     - create_products_table                                 │
│     - Schema builder pattern                                │
│                                                             │
│  5. ProductSeeder.php                                       │
│     - Seeder interface                                      │
│     - SeederRunner pattern                                  │
│                                                             │
│  6. routes/products.php                                     │
│     - Route definitions                                     │
│     - Middleware registration                               │
│                                                             │
│  All files follow EXACT same structure as existing modules  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 3.3 AI Debugging

```
┌─────────────────────────────────────────────────────────────┐
│                  AI DEBUGGING                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  PROBLEM: "User not found" error                            │
│                                                             │
│  AI CAN TRACE:                                              │
│  1. Request enters router                                   │
│  2. Router matches /users/{id}                              │
│  3. Middleware runs (auth, tenant)                           │
│  4. UserController::show() called                           │
│  5. UserRepository::findById() called                       │
│  6. sql-builder executes query                              │
│  7. Query returns null                                       │
│                                                             │
│  AI CAN FIX:                                                │
│  - Add validation in controller                             │
│  - Add try-catch in repository                              │
│  - Add proper error response                                │
│                                                             │
│  BECAUSE:                                                   │
│  - No hidden magic to confuse AI                            │
│  - Linear lifecycle is clear                                │
│  - Dependencies are explicit                                │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Package Architecture

### 4.1 Core Layer

```
┌─────────────────────────────────────────────────────────────┐
│                    CORE LAYER                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   Config    │  │  Container  │  │     Log     │        │
│  │             │  │             │  │             │        │
│  │ fromFile()  │  │ bind()      │  │ info()      │        │
│  │ fromDir()   │  │ singleton() │  │ error()     │        │
│  │ get()       │  │ instance()  │  │ debug()     │        │
│  │ set()       │  │ resolve()   │  │ warning()   │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   Event     │  │  Exception  │  │ Utilities   │        │
│  │             │  │             │  │             │        │
│  │ listen()    │  │ HttpExcept  │  │ Str::camel()│        │
│  │ dispatch()  │  │ DBExcept    │  │ Arr::get()  │        │
│  │ remove()    │  │ AuthExcept  │  │ Str::uuid() │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  Standalone: No dependencies between core packages          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 4.2 Database Layer

```
┌─────────────────────────────────────────────────────────────┐
│                  DATABASE LAYER                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                SQL Builder                           │   │
│  │                                                     │   │
│  │  DB::table('users')                                 │   │
│  │      ->where('status', '=', 'active')               │   │
│  │      ->orderBy('name')                              │   │
│  │      ->get();                                       │   │
│  │                                                     │   │
│  │  Dependencies: PDO only (native PHP)                │   │
│  └─────────────────────────────────────────────────────┘   │
│                         │                                   │
│        ┌────────────────┼────────────────┐                 │
│        ▼                ▼                ▼                 │
│  ┌───────────┐    ┌───────────┐    ┌───────────┐          │
│  │  Grammar  │    │ Migration │    │  Seeder   │          │
│  │           │    │           │    │           │          │
│  │ MySQL     │    │ Schema    │    │ Seeder    │          │
│  │ Postgres  │    │ Table     │    │ Runner    │          │
│  │ SQLite    │    │ Column    │    │           │          │
│  │ SQLServer │    │ Builder   │    │           │          │
│  └───────────┘    └───────────┘    └───────────┘          │
│                                                             │
│  Standalone: Can use sql-builder without migration/seeder   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 4.3 HTTP Layer

```
┌─────────────────────────────────────────────────────────────┐
│                    HTTP LAYER                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   Request   │  │  Response   │  │   Router    │        │
│  │             │  │             │  │             │        │
│  │ fromGlobals │  │ json()      │  │ get()       │        │
│  │ path()      │  │ html()      │  │ post()      │        │
│  │ method()    │  │ redirect()  │  │ put()       │        │
│  │ query()     │  │ withStatus()│  │ delete()    │        │
│  │ body()      │  │ withHeader()│  │ dispatch()  │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐                          │
│  │  Middleware  │  │   Session   │                          │
│  │             │  │             │                          │
│  │ pipe()      │  │ start()     │                          │
│  │ resolve()   │  │ get()       │                          │
│  │             │  │ set()       │                          │
│  │             │  │ flash()     │                          │
│  └─────────────┘  └─────────────┘                          │
│                                                             │
│  Standalone: Can use http without router                    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 4.4 Infrastructure Layer

```
┌─────────────────────────────────────────────────────────────┐
│                 INFRASTRUCTURE LAYER                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│  │  Cache   │ │   Mail   │ │  Queue   │ │  OAuth   │     │
│  │          │ │          │ │          │ │          │     │
│  │ Array    │ │ SMTP     │ │ Sync     │ │ Client   │     │
│  │ File     │ │ Mailable │ │ Database │ │ Provider │     │
│  │ Manager  │ │ Message  │ │ Manager  │ │ Tokens   │     │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘     │
│                                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│  │Scheduler │ │Notif     │ │WebSocket │ │   SSE    │     │
│  │          │ │          │ │          │ │          │     │
│  │ Cron     │ │ Mail     │ │ Server   │ │ Emitter  │     │
│  │ Interval │ │ Database │ │ Client   │ │ Channel  │     │
│  │ Tasks    │ │ Manager  │ │ Handler  │ │ Events   │     │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘     │
│                                                             │
│  Standalone: Each package is independent                    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 4.5 ERP Layer

```
┌─────────────────────────────────────────────────────────────┐
│                     ERP LAYER                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐                   │
│  │Accounting│ │Inventory │ │Purchasing│                   │
│  │          │ │          │ │          │                   │
│  │ Chart    │ │ Products │ │ Suppliers│                   │
│  │ Journal  │ │ Category │ │ PO       │                   │
│  │ Ledger   │ │ Stock    │ │ Receipts │                   │
│  │ Trial    │ │ Adjust   │ │ Invoices │                   │
│  └──────────┘ └──────────┘ └──────────┘                   │
│                                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐                   │
│  │  Sales   │ │   HR     │ │   CRM    │                   │
│  │          │ │          │ │          │                   │
│  │ Customer │ │ Employee │ │ Contact  │                   │
│  │ SO       │ │ Dept     │ │ Lead     │                   │
│  │ DN       │ │ Position │ │ Opps     │                   │
│  │ Invoice  │ │ Attend   │ │ Activity │                   │
│  │          │ │ Payroll  │ │ Notes    │                   │
│  └──────────┘ └──────────┘ └──────────┘                   │
│                                                             │
│  Each module follows same structure:                        │
│  - Controllers/                                            │
│  - Services/                                               │
│  - Repositories/                                           │
│  - routes/                                                 │
│  - migrations/                                             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. Standalone Usage Examples

### 5.1 PHP Native (No Framework)

```php
<?php
// file: index.php
// No framework, no container, no router

require_once 'vendor/autoload.php';

use Liberta\SqlBuilder\DB;
use Liberta\Cache\ArrayCache;
use Liberta\Log\FileLogger;

// Direct usage - no framework needed
$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'root', '');
$db = new DB($pdo);

$cache = new ArrayCache();
$logger = new FileLogger('/tmp/logs');

// Use them directly
$users = $db->table('users')->where('active', '=', true)->get();
$logger->info('Fetched ' . count($users) . ' users');

echo json_encode($users);
```

### 5.2 Add Features Incrementally

```php
<?php
// Step 1: Start with basic
composer require chandra/liberta-sql-builder
composer require chandra/liberta-config

// Step 2: Add HTTP
composer require chandra/liberta-http
composer require chandra/liberta-router

// Step 3: Add features as needed
composer require chandra/liberta-cache      // When you need caching
composer require chandra/liberta-queue      // When you need jobs
composer require chandra/liberta-mail       // When you need email
composer require chandra/liberta-websocket  // When you need realtime
```

### 5.3 Microservice Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                 MICROSERVICE SETUP                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  AUTH SERVICE                                               │
│  ├── liberta-jwt           → Token management               │
│  ├── liberta-rbac-client   → RBAC verification              │
│  ├── liberta-container     → DI container                   │
│  └── liberta-router        → HTTP routing                   │
│                                                             │
│  USER SERVICE                                               │
│  ├── liberta-sql-builder   → Database queries               │
│  ├── liberta-router        → HTTP routing                   │
│  └── liberta-cache         → User caching                   │
│                                                             │
│  ORDER SERVICE                                              │
│  ├── liberta-sql-builder   → Database queries               │
│  ├── liberta-queue         → Job processing                 │
│  ├── liberta-cache         → Order caching                  │
│  └── liberta-event         → Event dispatching              │
│                                                             │
│  NOTIFICATION SERVICE                                       │
│  ├── liberta-mail          → Email sending                  │
│  ├── liberta-notification  → Notification channels          │
│  ├── liberta-queue         → Async processing               │
│  └── liberta-sse           → Real-time push                 │
│                                                             │
│  Each service uses ONLY what it needs!                      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 6. Comparison with Other Frameworks

### 6.1 vs Laravel

```
┌─────────────────────────────────────────────────────────────┐
│              LIBERTA vs LARAVEL                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  LARAVEL:                                                   │
│  ├── Auto-scanning (magic)                                  │
│  ├── Service providers (hidden binding)                     │
│  ├── Facades (static magic)                                 │
│  ├── Artisan (tied to framework)                            │
│  ├── Eloquent (tight coupling)                              │
│  └── Monolithic (hard to use parts standalone)              │
│                                                             │
│  LIBERTA:                                                   │
│  ├── Explicit binding (no magic)                            │
│  ├── Manual DI (visible dependencies)                       │
│  ├── Direct instantiation (no facades)                      │
│  ├── CLI (standalone commands)                              │
│  ├── SQL Builder (loose coupling)                           │
│  └── Modular (use any package alone)                        │
│                                                             │
│  EXAMPLE:                                                   │
│                                                             │
│  // Laravel (hidden magic)                                  │
│  Route::get('/users', [UserController::class, 'index']);    │
│  // How does UserController get its dependencies?           │
│  // AI needs to understand service providers, containers,   │
│  // auto-resolution, etc.                                   │
│                                                             │
│  // Libertà (explicit)                                      │
│  $controller = new UserController($userRepository);         │
│  $router->get('/users', [$controller, 'index']);            │
│  // AI immediately knows:                                   │
│  // - UserController needs UserRepository                   │
│  // - UserRepository needs DB                               │
│  // - All dependencies are visible                          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 6.2 vs Symfony

```
┌─────────────────────────────────────────────────────────────┐
│              LIBERTA vs SYMFONY                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  SYMFONY:                                                   │
│  ├── Complex configuration (YAML/XML)                       │
│  ├── DependencyInjection component                          │
│  ├── Compiler passes (magic)                                │
│  ├── Service tags (hidden behavior)                         │
│  └── Steep learning curve                                   │
│                                                             │
│  LIBERTA:                                                   │
│  ├── Simple configuration (PHP arrays)                      │
│  ├── Manual DI (explicit)                                   │
│  ├── No compiler passes                                     │
│  ├── No service tags                                        │
│  └── Easy to learn                                          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 6.3 vs Native PHP

```
┌─────────────────────────────────────────────────────────────┐
│              LIBERTA vs NATIVE PHP                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  NATIVE PHP:                                                │
│  ├── No structure (chaos)                                   │
│  ├── No conventions (inconsistent)                          │
│  ├── No reusable components                                 │
│  ├── No testing utilities                                   │
│  └── No documentation standards                             │
│                                                             │
│  LIBERTA:                                                   │
│  ├── Consistent structure                                   │
│  ├── Strict naming conventions                              │
│  ├── Reusable packages                                      │
│  ├── Built-in testing utilities                             │
│  ├── Comprehensive documentation                            │
│  └── Still uses native PHP (PDO, curl, etc.)                │
│                                                             │
│  Libertà = Native PHP + Structure + Conventions             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 7. AI Benefits

### 7.1 Code Generation

```
┌─────────────────────────────────────────────────────────────┐
│                  AI CODE GENERATION                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  INPUT:                                                     │
│  "Create a new module for managing products"                │
│                                                             │
│  AI OUTPUT (Consistent Pattern):                            │
│                                                             │
│  ProductController.php                                      │
│  ├── __construct(ProductRepository $repo)                   │
│  ├── index(): Response                                      │
│  ├── show(int $id): Response                                │
│  ├── store(Request $request): Response                      │
│  ├── update(int $id, Request $request): Response            │
│  └── destroy(int $id): Response                             │
│                                                             │
│  ProductService.php                                         │
│  ├── __construct(ProductRepository $repo)                   │
│  ├── getAll(): array                                         │
│  ├── getById(int $id): ?array                               │
│  ├── create(array $data): int                               │
│  ├── update(int $id, array $data): bool                     │
│  └── delete(int $id): bool                                  │
│                                                             │
│  ProductRepository.php                                      │
│  ├── __construct(DB $db)                                    │
│  ├── all(): array                                           │
│  ├── find(int $id): ?array                                  │
│  ├── create(array $data): int                               │
│  ├── update(int $id, array $data): bool                     │
│  └── delete(int $id): bool                                  │
│                                                             │
│  All files follow SAME pattern as existing modules!         │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 7.2 Refactoring

```
┌─────────────────────────────────────────────────────────────┐
│                  AI REFACTORING                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  BEFORE:                                                    │
│  class UserRepository {                                     │
│      public function find($id) {                            │
│          $pdo = new PDO(...);                               │
│          $stmt = $pdo->query(...);                          │
│          return $stmt->fetch();                             │
│      }                                                      │
│  }                                                          │
│                                                             │
│  AI UNDERSTANDS:                                            │
│  - UserRepository creates PDO directly (bad)                │
│  - Should inject DB instead                                 │
│                                                             │
│  AFTER:                                                     │
│  class UserRepository {                                     │
│      public function __construct(private DB $db) {}         │
│      public function find($id): ?array {                    │
│          return $this->db->table('users')                   │
│              ->where('id', '=', $id)                        │
│              ->first();                                     │
│      }                                                      │
│  }                                                          │
│                                                             │
│  AI CAN SAFELY REFACTOR BECAUSE:                            │
│  - No hidden magic to break                                 │
│  - Dependencies are explicit                                │
│  - Tests exist for verification                             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 7.3 Debugging

```
┌─────────────────────────────────────────────────────────────┐
│                  AI DEBUGGING                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ERROR: "User not found"                                    │
│                                                             │
│  AI TRACE:                                                  │
│  1. Request → Router                                        │
│  2. Router → Middleware (auth, tenant)                       │
│  3. Middleware → UserController::show()                      │
│  4. UserController → UserRepository::findById()             │
│  5. UserRepository → DB::table('users')->where(...)         │
│  6. DB → PDO::query()                                       │
│  7. PDO → Returns null                                      │
│                                                             │
│  AI DIAGNOSIS:                                              │
│  - User doesn't exist in database                           │
│  - Or wrong tenant context                                  │
│  - Or wrong query conditions                                │
│                                                             │
│  AI FIX:                                                    │
│  - Add proper error handling                                │
│  - Add validation before query                              │
│  - Add logging for debugging                                │
│                                                             │
│  BECAUSE:                                                   │
│  - Linear lifecycle (no hidden steps)                       │
│  - Explicit dependencies (no magic)                         │
│  - Clear code structure (consistent)                        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. Summary

### 8.1 Key Benefits

| Benefit | Description |
|---------|-------------|
| **AI-Friendly** | Explicit code, no magic, consistent structure |
| **Standalone** | Each package works independently |
| **PHP Native** | Uses PDO, curl, native PHP features |
| **Modular** | Add only what you need |
| **Testable** | Unit tests for each package |
| **Documented** | README.md with examples for each package |

### 8.2 When to Use What

| Scenario | Packages |
|----------|----------|
| PHP Native Project | sql-builder, cache, log |
| Add HTTP | http, router |
| Add Background Jobs | queue, scheduler |
| Add Email | mail, notification |
| Add Realtime | websocket, sse |
| Add Auth | jwt, oauth, rbac-client |
| Add Multi-Tenant | tenant, connection |
| Full Framework | All packages |

### 8.3 Final Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  LIBERTA ARCHITECTURE                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                 APPLICATION LAYER                    │   │
│  │  (Use any combination of packages)                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                          │                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                 FRAMEWORK LAYER (Optional)           │   │
│  │  router + http + container + tenant                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                          │                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                 INFRASTRUCTURE LAYER                 │   │
│  │  cache + mail + queue + scheduler + oauth + ...      │   │
│  └─────────────────────────────────────────────────────┘   │
│                          │                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                 DATABASE LAYER                       │   │
│  │  sql-builder + grammar + migration + seeder          │   │
│  └─────────────────────────────────────────────────────┘   │
│                          │                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                 CORE LAYER                           │   │
│  │  config + container + log + event + exception        │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │                 PHP NATIVE                           │   │
│  │  PDO + curl + sessions + streams                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

*Generated: 2026-07-20*
*Author: Chandra Mahardika — chandra.libertania@gmail.com*
*Liberta Microservice Framework — AI-Friendly, Standalone, PHP Native*
