# GIT_STEP.md — Push Liberta Packages ke GitHub

Panduan langkah demi langkah untuk menjadikan setiap package sebagai GitHub repository terpisah yang bisa di-`require` via Composer.

---

## Overview

- **25 library packages** — masing-masing 1 repo GitHub
- **2 application projects** — masing-masing 1 repo GitHub
- **Vendor name:** `chandra`
- **Package name format:** `chandra/liberta-<name>`

---

## Prasyarat

1. Akun GitHub dengan username **`chandra`** (atau ganti vendor name di semua `composer.json`)
2. [GitHub CLI](https://cli.github.com/) (`gh`) terinstall dan ter-authenticasi
3. Git terinstall
4. PowerShell

Cek kesiapan:

```powershell
git --version
gh auth status
```

---

## Langkah 1: Persiapan File

Buat `.gitignore` di **setiap package** (jika belum ada):

```
/vendor/
composer.lock
*.log
.DS_Store
Thumbs.db
.phpunit.cache
.phpstan.cache
```

Buat file `LICENSE` (MIT) di **setiap package** jika belum ada.

---

## Langkah 2: Setup Per-Package (Manual)

Ulangi untuk setiap package:

```powershell
# 1. Masuk ke direktori package
cd D:\container-projects\www\liberta\liberta-container

# 2. Init git
git init
git branch -M main

# 3. Tambah semua file
git add .

# 4. Commit pertama
git commit -m "feat: initial release v1.0.0"

# 5. Buat repo di GitHub dan push
gh repo create chandra/liberta-container --public --source=. --remote=origin --push

# 6. Tag versi pertama
git tag 1.0.0
git push --tags
```

---

## Langkah 3: Publish Semua Package (Otomatis)

Jalankan script `publish-all.ps1` di root project:

```powershell
cd D:\container-projects\www\liberta
.\publish-all.ps1
```

Script ini akan:
1. Loop semua 25 library packages
2. Init git di setiap package
3. Buat `.gitignore` jika belum ada
4. Commit semua file
5. Buat repo di GitHub via `gh` CLI
6. Tag `1.0.0` dan push

---

## Langkah 4: Publish Application Projects

```powershell
# Auth Service
cd D:\container-projects\www\liberta\liberta-auth-service
git init
git branch -M main
git add .
git commit -m "feat: initial release v1.0.0"
gh repo create chandra/liberta-auth-service --public --source=. --remote=origin --push
git tag 1.0.0
git push --tags

# Business Service
cd D:\container-projects\www\liberta\liberta-bussiness-service
git init
git branch -M main
git add .
git commit -m "feat: initial release v1.0.0"
gh repo create chandra/liberta-bussiness-service --public --source=. --remote=origin --push
git tag 1.0.0
git push --tags
```

---

## Langkah 5: Verifikasi

Cek semua repo sudah terbuka:

```powershell
gh repo list chandra --limit 50
```

Cek salah satu package bisa di-require:

```powershell
mkdir test-install
cd test-install
composer require chandra/liberta-container
```

---

## Versioning (Release Baru)

Setiap kali ada perubahan, push commit baru lalu tag versi berikutnya:

```powershell
cd D:\container-projects\www\liberta\liberta-container

git add .
git commit -m "fix: perbaikan bugs"
git push

# Patch version
git tag 1.0.1
git push --tags

# Minor version
git tag 1.1.0
git push --tags

# Major version
git tag 2.0.0
git push --tags
```

User bisa require versi spesifik:

```json
{
    "require": {
        "chandra/liberta-container": "^1.0"
    }
}
```

---

## Internal Dependencies

Beberapa package saling depend. Ini sudah ditangani dengan benar di `composer.json`:

| Package | Depends On |
|---------|-----------|
| `liberta-queue` | `chandra/liberta-sql-builder: ^1.0` |
| `liberta-oauth` | `chandra/liberta-jwt: ^1.0`, `chandra/liberta-sql-builder: ^1.0` |
| `liberta-migration` | `chandra/liberta-sql-builder: ^1.0` |
| `liberta-notification` | `chandra/liberta-mail: ^1.0`, `chandra/liberta-sql-builder: ^1.0` |
| `liberta-seeder` | `chandra/liberta-sql-builder: ^1.0` |
| `liberta-connection` | `chandra/liberta-sql-builder: ^1.0` |
| `liberta-tenant` | `chandra/liberta-connection: ^1.0` |
| `liberta-erp` | `chandra/liberta-router: ^1.0`, `chandra/liberta-sql-builder: ^1.0` |

> **Pastikan semua dependency sudah ter-publish dulu sebelum package yang depend di-publish.**

### Urutan Publish yang Disarankan

Publish berdasarkan dependency graph (bottom-up):

1. **Wave 1** (zero dependencies):
   `liberta-config`, `liberta-container`, `liberta-log`, `liberta-event`, `liberta-exception`, `liberta-utilities`, `liberta-sql-builder`, `liberta-grammar`, `liberta-http`, `liberta-router`, `liberta-session`, `liberta-cache`, `liberta-mail`, `liberta-scheduler`, `liberta-websocket`, `liberta-sse`, `liberta-cli`, `liberta-report`

2. **Wave 2** (depend on Wave 1):
   `liberta-migration`, `liberta-seeder`, `liberta-connection`, `liberta-queue`, `liberta-jwt`, `liberta-rbac-client`, `liberta-erp`

3. **Wave 3** (depend on Wave 2):
   `liberta-oauth`, `liberta-notification`, `liberta-tenant`

4. **Wave 4** (application projects):
   `liberta-auth-service`, `liberta-bussiness-service`

---

## Troubleshooting

### Repo sudah ada di GitHub

```powershell
gh repo view chandra/liberta-container
# Jika sudah ada, skip pembuatan repo
git remote add origin https://github.com/chandra/liberta-container.git
git push -u origin main
```

### Auth-service pakai nama dependency salah

`liberta-auth-service/composer.json` pakai `liberta/router` bukan `chandra/liberta-router`.

Perbaiki di `composer.json` auth-service:

```json
{
    "require": {
        "chandra/liberta-router": "^1.0",
        "chandra/liberta-http": "^1.0",
        "chandra/liberta-sql-builder": "^1.0",
        "firebase/php-jwt": "^6.10"
    }
}
```

### Mau pakai private repo

Ganti `--public` ke `--private` di perintah `gh repo create`:

```powershell
gh repo create chandra/liberta-container --private --source=. --remote=origin --push
```

User perlu GitHub authentication untuk install:

```bash
# Via personal access token
COMPOSER_AUTH='{"github-oauth":{"github.com":"ghp_xxxxx"}}' composer require chandra/liberta-container

# Atau di auth.json
{
    "github-oauth": {
        "github.com": "ghp_xxxxx"
    }
}
```

---

## Monorepo Alternative

Jika ingin tetap satu repo tapi publish per package, bisa pakai **subtree split**:

```bash
# Split package dari monorepo
git subtree split --prefix=liberta-container -b liberta-container-split

# Push ke repo terpisah
git push origin liberta-container-split:main --force
```

Atau pakai tools seperti:
- [satis](https://getcomposer.org/doc/articles/hierarchical-repository.md) — private Composer repository
- [Lerna](https://lerna.js.org/) — monorepo management
- [Monorepo Builder](https://github.com/symplify/monorepo-builder)

---

## Checklist

- [ ] `.gitignore` ada di semua package
- [ ] `LICENSE` ada di semua package
- [ ] Semua `composer.json` sudah benar (nama, autoload, dependencies)
- [ ] Perbaiki auth-service dependency names (`liberta/*` → `chandra/liberta-*`)
- [ ] Publish Wave 1 packages
- [ ] Publish Wave 2 packages
- [ ] Publish Wave 3 packages
- [ ] Publish Wave 4 (apps)
- [ ] Verifikasi `composer require` berhasil
