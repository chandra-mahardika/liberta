<?php

namespace Liberta\Tenant;

use Liberta\Connection\ConnectionManager;

/**
 * Static holder for the current tenant context.
 *
 * Usage in bootstrap:
 *   TenantContext::resolve($request, $resolver, $connectionManager);
 *
 * Usage downstream:
 *   $db = TenantContext::db();       // gets DB for current tenant
 *   $id = TenantContext::tenantId(); // gets current tenant ID
 */
class TenantContext
{
    private static ?string $tenantId = null;
    private static ?ConnectionManager $connectionManager = null;
    private static bool $resolved = false;

    /**
     * Resolve tenant from request and set active connection.
     */
    public static function resolve(
        \Liberta\Http\Request $request,
        TenantResolver $resolver,
        ConnectionManager $manager
    ): void {
        $tenantId = $resolver->resolve($request);

        if ($tenantId !== null && $manager->has($tenantId)) {
            self::$tenantId = $tenantId;
            $manager->setActive($tenantId);
        }

        self::$connectionManager = $manager;
        self::$resolved = true;
    }

    /**
     * Get the current tenant ID (null if no tenant / default).
     */
    public static function tenantId(): ?string
    {
        return self::$tenantId;
    }

    /**
     * Get the DB instance for the current tenant.
     */
    public static function db(): \Liberta\Sql\DB
    {
        if (self::$connectionManager === null) {
            throw new \RuntimeException(
                'TenantContext not initialized. Call TenantContext::resolve() first.'
            );
        }

        return self::$connectionManager->connection();
    }

    /**
     * Get the ConnectionManager instance.
     */
    public static function connectionManager(): ConnectionManager
    {
        if (self::$connectionManager === null) {
            throw new \RuntimeException(
                'TenantContext not initialized. Call TenantContext::resolve() first.'
            );
        }

        return self::$connectionManager;
    }

    /**
     * Check if a tenant has been resolved.
     */
    public static function isResolved(): bool
    {
        return self::$resolved;
    }

    /**
     * Reset the context (useful in tests or long-running processes).
     */
    public static function clear(): void
    {
        self::$tenantId = null;
        self::$connectionManager = null;
        self::$resolved = false;
    }
}
