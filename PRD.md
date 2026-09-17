````markdown
# Product Requirement Document (PRD)
# Liberta Microservice Framework (LMF)

**Version:** 1.0.0 (Draft)  
**Status:** Draft  
**Project:** Liberta Microservice Framework  
**Target Platform:** PHP 8.3+  
**Architecture:** Native PHP Microservice Framework  
**License:** Open Source (TBD)

---

# 1. Vision

Membangun sebuah framework microservice berbasis Native PHP yang ringan, transparan, modular, dan **AI-Friendly**, sehingga developer dapat memahami seluruh alur aplikasi tanpa bergantung pada mekanisme yang bersifat *magic* maupun abstraksi yang berlebihan.

Liberta tidak berusaha menjadi framework dengan fitur terbanyak, melainkan menjadi framework yang paling mudah dipahami, dikembangkan, dan dipelihara.

> **"Framework should help developers think, not replace how developers think."**

---

# 2. Background

Perkembangan framework modern memberikan kemudahan yang luar biasa, namun juga menghadirkan kompleksitas yang semakin tinggi.

Beberapa permasalahan yang sering ditemui antara lain:

- lifecycle framework sulit dipahami
- dependency injection yang terlalu kompleks
- proses bootstrap yang tersembunyi
- terlalu banyak convention yang tidak terlihat
- debugging menjadi sulit
- AI coding assistant kesulitan memahami alur framework

Liberta hadir untuk menjawab permasalahan tersebut melalui pendekatan yang lebih eksplisit dan sederhana.

---

# 3. Product Vision

Liberta bertujuan menjadi fondasi utama dalam membangun:

- REST API
- ERP
- CRM
- HRIS
- Financial System
- SaaS Platform
- Enterprise Application
- Native Microservice
- Internal Company Platform

---

# 4. Core Philosophy

Seluruh keputusan teknis di Liberta harus selalu mengacu pada filosofi berikut.

## 4.1 Explicit Over Magic

Tidak ada proses tersembunyi.

Developer harus dapat mengikuti alur request hanya dengan membaca source code.

Seluruh object dibuat secara eksplisit.

Tidak ada auto scanning.

Tidak ada auto binding.

Tidak ada dependency yang muncul secara otomatis.

---

## 4.2 AI Friendly

Liberta dirancang agar mudah dipahami oleh AI.

Karakteristiknya:

- struktur folder konsisten
- naming convention ketat
- dependency eksplisit
- lifecycle linear
- sedikit abstraksi
- dokumentasi lengkap

Semakin mudah dipahami AI, semakin tinggi kualitas kode yang dapat dihasilkan.

---

## 4.3 Native PHP First

Liberta memanfaatkan kemampuan asli PHP sebanyak mungkin.

Menggunakan:

- PDO
- PSR Standard
- Enum
- Attributes
- Readonly Property
- Anonymous Function
- Native Exception

Tidak membuat abstraksi jika PHP telah menyediakan solusi yang baik.

---

## 4.4 Package Driven

Framework bukan kumpulan fitur.

Framework adalah kumpulan package independen.

Core hanya menyediakan pondasi.

---

## 4.5 Minimal Core

Core hanya menangani:

- bootstrap
- request
- response
- routing
- middleware
- configuration

Semua fitur lain dipisahkan menjadi package.

---

# 5. Product Goals

Liberta harus memenuhi tujuan berikut.

## Functional Goals

- Native Microservice Framework
- Modular Package
- REST API Ready
- Enterprise Ready
- ERP Ready
- High Performance

## Technical Goals

- mudah dipahami
- mudah di-debug
- mudah dipelajari
- mudah dikembangkan
- mudah diintegrasikan

## AI Goals

- AI dapat memahami source code tanpa konfigurasi tambahan
- AI dapat menghasilkan module baru secara konsisten
- AI dapat melakukan refactor dengan risiko minimal

---

# 6. Non Goals

Liberta tidak bertujuan menjadi:

- Laravel Clone
- Symfony Clone
- CMS
- Wordpress Alternative
- Fullstack Frontend Framework
- Visual Builder
- Low Code Platform

---

# 7. Target Users

## Primary

- Backend Developer
- ERP Developer
- Enterprise Team
- Startup Team

## Secondary

- ChatGPT
- Codex
- Claude
- Gemini
- GitHub Copilot
- Cursor

---

# 8. Request Lifecycle

```text
HTTP Request

↓

Bootstrap

↓

Router

↓

Middleware Pipeline

↓

Controller

↓

Service

↓

Repository

↓

Database

↓

Response
```

Prinsip utama:

Seluruh lifecycle harus dapat dipahami tanpa debugger.

---

# 9. Architecture

```text
Application

↓

Router

↓

Controller

↓

Service

↓

Repository

↓

Database
```

Dependency selalu satu arah.

Tidak boleh ada circular dependency.

---

# 10. Folder Structure

```text
app/
bootstrap/
config/
modules/
packages/
routes/
storage/
public/
vendor/
```

---

# 11. Module Structure

```text
Modules/

User/

Controllers/
Services/
Repositories/
Models/
Routes/
Config/
Resources/
Tests/
```

Semua module menggunakan struktur yang sama.

---

# 12. Package Structure

```text
Package/

src/
Config/
Routes/
Resources/
Tests/

composer.json
README.md
```

---

# 13. Dependency Injection

Versi pertama Liberta tidak menggunakan IoC Container.

Dependency dibuat secara manual.

Contoh:

```php
$userRepository = new UserRepository();

$userService = new UserService(
    $userRepository
);

$userController = new UserController(
    $userService
);
```

Dengan pendekatan ini seluruh dependency dapat terlihat secara eksplisit.

---

# 14. Routing

Routing bersifat deklaratif.

Contoh:

```php
Router::get('/users', UserController::class);

Router::post('/users', StoreUserController::class);
```

Tidak menggunakan annotation.

Tidak melakukan scanning folder.

---

# 15. Middleware

Pipeline middleware sederhana.

```text
Request

↓

Auth

↓

Permission

↓

Logging

↓

Controller
```

---

# 16. Controller

Controller hanya memiliki tanggung jawab:

- menerima request
- validasi request
- memanggil service
- mengembalikan response

Tidak boleh terdapat business logic.

---

# 17. Service Layer

Service merupakan tempat seluruh business rule.

Service boleh:

- menggunakan Repository
- menggunakan package lain
- melakukan transaction
- melakukan orchestration

---

# 18. Repository Layer

Repository hanya menangani:

- Query Database
- CRUD
- Transaction
- Persistence

Tidak boleh terdapat business rule.

---

# 19. Configuration

Semua konfigurasi berada pada folder:

```text
config/
```

Tidak ada konfigurasi tersembunyi.

---

# 20. Error Handling

Semua exception harus:

- konsisten
- mudah ditelusuri
- memiliki log
- memiliki response standar

---

# 21. Logging

Minimal menyediakan:

- Info
- Warning
- Error
- Debug

Format log harus mudah dibaca manusia maupun AI.

---

# 22. Package Roadmap

Package resmi Liberta:

- Authentication
- RBAC
- SQL Builder
- Migration
- Seeder
- Validation
- JWT
- OAuth
- Storage
- Queue
- Scheduler
- Mail
- Notification
- Event
- SSE
- WebSocket
- Audit Log
- HTTP Client
- Cache
- OpenAPI

---

# 23. Microservice Philosophy

Setiap service harus:

- Independent
- Deployable
- Observable
- Replaceable
- Versionable

Komunikasi antar service dilakukan melalui:

- REST
- Message Queue
- Event

Tidak diperbolehkan mengakses database service lain secara langsung.

---

# 24. Performance Goals

Target performa Core Framework:

| Item | Target |
|-------|--------|
| Bootstrap | < 5 ms |
| Routing | < 1 ms |
| Memory Idle | < 2 MB |
| Startup | Sangat Cepat |

---

# 25. Development Principles

Seluruh pengembangan Liberta harus mengikuti prinsip berikut.

- KISS (Keep It Simple)
- SOLID
- DRY
- Explicit Coding
- Composition over Inheritance
- Package First
- Native First

---

# 26. Coding Standard

- PSR-12
- Strict Type
- Constructor Injection
- Immutable Object jika memungkinkan
- Readonly Property
- Typed Property
- Typed Return

---

# 27. Success Metrics

Liberta dianggap berhasil apabila:

- Developer memahami framework dalam waktu kurang dari satu jam.
- AI coding assistant dapat menghasilkan kode dengan tingkat konsistensi tinggi.
- Seluruh package mengikuti konvensi yang sama.
- Core framework tetap kecil dan mudah diaudit.
- Sebuah microservice CRUD dapat dibuat dalam waktu kurang dari 30 menit.

---

# 28. Roadmap

## Phase 1

- Bootstrap
- Request
- Response
- Router
- Middleware
- Controller

## Phase 2

- Database
- SQL Builder
- Migration
- Seeder
- Validation

## Phase 3

- Authentication
- RBAC
- JWT

## Phase 4

- Queue
- Scheduler
- Event
- Notification

## Phase 5

- Service Discovery
- Distributed Config
- Distributed Logging
- Distributed Tracing
- API Gateway Integration

---

# 29. Closing Statement

Liberta bukan sekadar framework.

Liberta adalah filosofi dalam membangun perangkat lunak yang:

- sederhana
- transparan
- modular
- cepat dipahami
- mudah dipelihara
- AI-Friendly

Framework ini dibangun dengan keyakinan bahwa **kesederhanaan bukan berarti keterbatasan**, melainkan fondasi untuk menciptakan sistem yang lebih kuat, lebih mudah berkembang, dan lebih tahan terhadap perubahan teknologi di masa depan.
````
