<?php

namespace Liberta\Tenant;

use Liberta\Http\Request;

/**
 * Resolves tenant from the request subdomain.
 *
 * Example: company-a.api.example.com → tenant = "company-a"
 */
class SubdomainTenantResolver implements TenantResolver
{
    public function __construct(
        protected string $baseDomain = ''
    ) {}

    public function resolve(Request $request): ?string
    {
        $host = $request->headers()['Host'] ?? $request->headers()['host'] ?? '';

        if ($host === '') {
            return null;
        }

        // Strip port
        $host = explode(':', $host)[0];

        // If baseDomain is set, strip it to get the subdomain
        if ($this->baseDomain !== '') {
            $suffix = '.' . ltrim($this->baseDomain, '.');

            if (str_ends_with($host, $suffix)) {
                $subdomain = substr($host, 0, -strlen($suffix));
                return $subdomain !== '' ? $subdomain : null;
            }

            return null;
        }

        // Without baseDomain, extract first segment as tenant
        $parts = explode('.', $host);

        if (count($parts) > 2) {
            return $parts[0];
        }

        return null;
    }
}
