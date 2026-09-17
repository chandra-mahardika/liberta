# publish-all.ps1
# Publish semua Liberta packages ke GitHub
# Jalankan dari root project: .\publish-all.ps1

$ErrorActionPreference = "Stop"
$base = $PSScriptRoot

$packages = @(
    # Wave 1 — zero dependencies
    "liberta-config"
    "liberta-container"
    "liberta-log"
    "liberta-event"
    "liberta-exception"
    "liberta-utilities"
    "liberta-sql-builder"
    "liberta-grammar"
    "liberta-http"
    "liberta-router"
    "liberta-session"
    "liberta-cache"
    "liberta-mail"
    "liberta-scheduler"
    "liberta-websocket"
    "liberta-sse"
    "liberta-cli"
    "liberta-report"

    # Wave 2 — depend on Wave 1
    "liberta-migration"
    "liberta-seeder"
    "liberta-connection"
    "liberta-queue"
    "liberta-jwt"
    "liberta-rbac-client"
    "liberta-erp"

    # Wave 3 — depend on Wave 2
    "liberta-oauth"
    "liberta-notification"
    "liberta-tenant"
)

$apps = @(
    "liberta-auth-service"
    "liberta-bussiness-service"
)

$gitignoreContent = "/vendor/
composer.lock
*.log
.DS_Store
Thumbs.db
.phpunit.cache
.phpstan.cache"

$success = @()
$failed = @()

function Publish-Package {
    param(
        [string]$name,
        [string]$type
    )

    $path = Join-Path $base $name

    if (-not (Test-Path $path)) {
        Write-Host "[SKIP] $name — directory not found" -ForegroundColor Yellow
        $failed += $name
        return
    }

    Write-Host ""
    Write-Host "============================================" -ForegroundColor Cyan
    Write-Host "  Publishing: $name ($type)" -ForegroundColor Cyan
    Write-Host "============================================" -ForegroundColor Cyan

    Set-Location $path

    try {
        # Init git jika belum ada
        if (-not (Test-Path ".git")) {
            git init
            if ($LASTEXITCODE -ne 0) { throw "git init failed" }

            git branch -M main
            if ($LASTEXITCODE -ne 0) { throw "git branch failed" }
        }

        # Buat .gitignore jika belum ada
        if (-not (Test-Path ".gitignore")) {
            Set-Content -Path ".gitignore" -Value $gitignoreContent -Encoding UTF8
            Write-Host "  Created .gitignore" -ForegroundColor Gray
        }

        # Tambah semua file
        git add -A
        if ($LASTEXITCODE -ne 0) { throw "git add failed" }

        # Cek apakah ada yang perlu di-commit
        $status = git status --porcelain
        if ($status) {
            git commit -m "feat: initial release v1.0.0"
            if ($LASTEXITCODE -ne 0) { throw "git commit failed" }
        } else {
            Write-Host "  Nothing to commit, using --allow-empty" -ForegroundColor Gray
            git commit --allow-empty -m "feat: initial release v1.0.0"
            if ($LASTEXITCODE -ne 0) { throw "git commit failed" }
        }

        # Cek apakah repo sudah ada di GitHub
        $repoName = "chandra/$name"
        $repoExists = gh repo view $repoName 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  Repo $repoName already exists, skipping creation" -ForegroundColor Yellow
            # Pastikan remote ada
            $remoteUrl = git remote get-url origin 2>$null
            if ($LASTEXITCODE -ne 0) {
                git remote add origin "https://github.com/$repoName.git"
            }
        } else {
            # Buat repo baru di GitHub
            $visibility = if ($type -eq "app") { "--private" } else { "--public" }
            gh repo create $repoName --source=. --remote=origin --push $visibility
            if ($LASTEXITCODE -ne 0) { throw "gh repo create failed" }
        }

        # Push jika remote sudah ada tapi local belum push
        git push -u origin main 2>$null

        # Tag v1.0.0 jika belum ada
        $tagExists = git tag -l "1.0.0"
        if (-not $tagExists) {
            git tag 1.0.0
            if ($LASTEXITCODE -ne 0) { throw "git tag failed" }
        }

        # Push tags
        git push --tags
        if ($LASTEXITCODE -ne 0) { throw "git push tags failed" }

        Write-Host "[OK] $name published successfully" -ForegroundColor Green
        $script:success += $name

    } catch {
        Write-Host "[FAIL] $name — $($_.Exception.Message)" -ForegroundColor Red
        $script:failed += $name
    }
}

# ========================
# Main Execution
# ========================

Write-Host ""
Write-Host "Liberta Package Publisher" -ForegroundColor White
Write-Host "Base path: $base" -ForegroundColor Gray
Write-Host ""

# Publish library packages
Write-Host ">>> Publishing library packages ($($packages.Count) total)..." -ForegroundColor White
foreach ($pkg in $packages) {
    Publish-Package -name $pkg -type "library"
}

# Publish application projects
Write-Host ""
Write-Host ">>> Publishing application projects ($($apps.Count) total)..." -ForegroundColor White
foreach ($app in $apps) {
    Publish-Package -name $app -type "app"
}

# Kembali ke base
Set-Location $base

# Summary
Write-Host ""
Write-Host "============================================" -ForegroundColor White
Write-Host "  SUMMARY" -ForegroundColor White
Write-Host "============================================" -ForegroundColor White
Write-Host "  Success: $($success.Count)" -ForegroundColor Green
Write-Host "  Failed:  $($failed.Count)" -ForegroundColor Red
Write-Host "  Total:   $($success.Count + $failed.Count)" -ForegroundColor White

if ($failed.Count -gt 0) {
    Write-Host ""
    Write-Host "  Failed packages:" -ForegroundColor Red
    foreach ($f in $failed) {
        Write-Host "    - $f" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Done!" -ForegroundColor Cyan
