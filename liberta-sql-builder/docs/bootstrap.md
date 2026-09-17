# Contoh Bootstrap Microservice Native PHP

Dokumen ini menunjukkan **contoh struktur dan bootstrap microservice native PHP** yang menggunakan package **arsitektur/query-builder**.

Tujuan:

* Tanpa framework
* Struktur jelas & scalable
* Siap dikembangkan ke arah RBAC / Auth Service / Business Service

---

## 📁 Struktur Folder

```text
microservice-user/
 ├── public/
 │    └── index.php
 ├── app/
 │    ├── Bootstrap/
 │    │    ├── App.php
 │    │    └── Database.php
 │    ├── Http/
 │    │    ├── Controllers/
 │    │    │    └── UserController.php
 │    │    └── Request.php
 │    └── Repositories/
 │         └── UserRepository.php
 ├── config/
 │    └── database.php
 ├── vendor/
 ├── composer.json
 └── .env
```

---

## 📦 composer.json

```json
{
  "name": "microservice/user",
  "type": "project",
  "require": {
    "php": "^8.2",
    "arsitektur/query-builder": "^1.0"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}
```

Setelah install:

```bash
composer install
composer dump-autoload
```

---

## ⚙️ config/database.php

```php
<?php

return [
    'driver' => 'mysql', // mysql | sqlsrv
    'host' => '127.0.0.1',
    'database' => 'app_db',
    'username' => 'root',
    'password' => 'secret',
];
```

---

## ⚙️ app/Bootstrap/Database.php

```php
<?php

namespace App\Bootstrap;

use PDO;
use Liberta\Sql\DB;
use Liberta\Sql\Connection\Connection;

class Database
{
    public static function init(): void
    {
        $config = require __DIR__ . '/../../config/database.php';

        $dsn = match ($config['driver']) {
            'sqlsrv' => "sqlsrv:Server={$config['host']};Database={$config['database']}",
            default  => "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        };

        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        DB::setConnection(new Connection($pdo, $config['driver']));
    }
}
```

---

## 🚀 app/Bootstrap/App.php

```php
<?php

namespace App\Bootstrap;

class App
{
    public static function run(): void
    {
        Database::init();
    }
}
```

---

## 🌐 public/index.php

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap\App;
use App\Http\Controllers\UserController;

App::run();

$controller = new UserController();

header('Content-Type: application/json');
echo json_encode($controller->index());
```

---

## 🧠 Repository Pattern (Disarankan)

### app/Repositories/UserRepository.php

```php
<?php

namespace App\Repositories;

use Liberta\Sql\DB;

class UserRepository
{
    public function all(): array
    {
        return DB::table('users')
            ->select('id', 'name', 'email')
            ->orderBy('id', 'desc')
            ->get();
    }
}
```

---

## 🎮 Controller

### app/Http/Controllers/UserController.php

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;

class UserController
{
    protected UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function index(): array
    {
        return [
            'status' => 'success',
            'data' => $this->users->all()
        ];
    }
}
```

---

## 🧩 Kenapa Struktur Ini Masuk Akal

* Tidak tergantung framework
* Mudah dipecah menjadi microservice lain
* Query Builder terisolasi di Repository
* Bootstrap jelas & eksplisit
* Cocok untuk Auth Service, Property Service, Accounting Service

---

## 🚦 Next Evolution (Opsional)

* Tambah Router sederhana
* Middleware (Auth, RBAC)
* Centralized Auth Service
* Standardized API Response

> Struktur ini **sengaja sederhana**, supaya mudah tumbuh tanpa refactor besar.
