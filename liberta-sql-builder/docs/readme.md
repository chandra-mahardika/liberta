# Arsitektur Query Builder

Lightweight, framework-agnostic **Query Builder for PHP microservices**.

Package ini **bukan ORM**, tidak memiliki Model, Relation, atau Magic Method. Fokus utama adalah **Data Access Layer yang ringan, konsisten, dan aman** untuk arsitektur microservice.

---

## ✨ Fitur Utama

* Query Builder ala Laravel (tanpa Eloquent)
* PDO prepared statement (aman dari SQL Injection)
* Mendukung **MySQL** dan **SQL Server**
* Stateless & reusable
* Cocok untuk microservice native PHP
* Composer-ready

---

## ❌ Yang Tidak Disediakan (by design)

* Model / Active Record
* Relation / Eager Loading
* Migration / Schema Builder
* Pagination Object
* Event / Observer

> Jika kamu membutuhkan fitur-fitur di atas, gunakan framework penuh.

---

## 📦 Instalasi

Tambahkan package melalui Composer:

```bash
composer require arsitektur/query-builder
```

Minimal requirement:

* PHP ^8.2

---

## ⚙️ Bootstrap (Wajib)

Package ini **tidak mengatur koneksi database**. Kamu harus melakukan bootstrap sendiri di setiap microservice.

### Contoh Bootstrap PDO

```php
use Liberta\Sql\DB;
use Liberta\Sql\Connection\Connection;

$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=test',
    'root',
    'secret',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$connection = new Connection($pdo, 'mysql'); // mysql | sqlsrv
DB::setConnection($connection);
```

Untuk SQL Server:

```php
$connection = new Connection($pdo, 'sqlsrv');
DB::setConnection($connection);
```

---

## 🚀 Contoh Penggunaan

### SELECT

```php
use Liberta\Sql\DB;

$users = DB::table('users')
    ->select('id', 'name')
    ->where('status', '=', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
```

---

### WHERE & OR WHERE

```php
DB::table('users')
    ->where('role', '=', 'admin')
    ->orWhere('role', '=', 'superadmin')
    ->get();
```

---

### WHERE IN

```php
DB::table('users')
    ->whereIn('id', [1, 2, 3])
    ->get();
```

---

### JOIN

```php
DB::table('users')
    ->select('users.id', 'profiles.phone')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->get();
```

---

### INSERT

```php
DB::table('users')->insert([
    'name'  => 'Dyah',
    'email' => 'dyah@example.com'
]);
```

---

### UPDATE (Wajib WHERE)

```php
DB::table('users')
    ->where('id', '=', 1)
    ->update([
        'name' => 'Dyah Galuh'
    ]);
```

---

### DELETE (Wajib WHERE)

```php
DB::table('users')
    ->where('id', '=', 1)
    ->delete();
```

---

## 🧪 Debugging

### Lihat SQL

```php
$sql = DB::table('users')
    ->where('id', '=', 1)
    ->toSql();
```

### Lihat Binding

```php
$bindings = DB::table('users')
    ->where('id', '=', 1)
    ->getBindings();
```

---

## 🧠 Filosofi Desain

* Query Builder ≠ ORM
* Builder hanya menyusun query
* Grammar bertanggung jawab pada dialect SQL
* Connection di-inject dari luar
* Aman untuk distributed system

---

## 🛣 Roadmap

* V1.1 Transaction Support
* V1.2 Batch Insert
* V2 Repository Helper (optional)

---

## 📄 Lisensi

MIT License

---

## 👤 Author

Dibangun untuk kebutuhan arsitektur microservice native PHP.

Jika kamu mencari Query Builder yang **ringan, eksplisit, dan maintainable**, package ini dibuat untukmu.
