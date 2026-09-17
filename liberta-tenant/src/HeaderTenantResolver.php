<?php

namespace Liberta\Tenant;

use Liberta\Http\Request;

/**
 * Resolves tenant from a request header.
 *
 * Default header: X-Tenant-ID
 */
class HeaderTenantResolver implements TenantResolver
{
    public function __construct(
        protected string $header = 'X-Tenant-ID'
    ) {}

    public function resolve(Request $request): ?string
    {
        $headers = $request->headers();
        return $headers[$this->header] ?? $headers[strtolower($this->header)] ?? null;
    }
}
