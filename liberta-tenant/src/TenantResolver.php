<?php

namespace Liberta\Tenant;

use Liberta\Connection\ConnectionManager;

/**
 * Resolves the current tenant from an incoming request.
 *
 * Implementations can resolve tenant from:
 * - Subdomain (company.example.com)
 * - Header (X-Tenant-ID)
 * - JWT claim
 * - Path prefix (/company-a/api/...)
 */
interface TenantResolver
{
    /**
     * Resolve tenant ID from the request.
     * Return null if no tenant is identified (use default).
     */
    public function resolve(\Liberta\Http\Request $request): ?string;
}
