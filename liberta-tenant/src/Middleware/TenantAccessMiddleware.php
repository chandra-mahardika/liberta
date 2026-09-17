<?php

declare(strict_types=1);

namespace Liberta\Tenant;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Router\Middleware\MiddlewareInterface;
use Closure;

class TenantAccessMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $header = 'X-Tenant-ID',
        private bool $required = true,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->headers()[$this->header]
            ?? $request->headers()[strtolower($this->header)]
            ?? null;

        if ($tenantId === null) {
            if ($this->required) {
                return new Response(
                    ['success' => false, 'message' => 'Tenant ID required'],
                    400
                );
            }

            return $next($request);
        }

        $user = $request->user();

        if ($user === null) {
            return new Response(
                ['success' => false, 'message' => 'Authentication required'],
                401
            );
        }

        if (!$this->userBelongsToTenant($user, $tenantId)) {
            return new Response(
                ['success' => false, 'message' => 'Access denied to this tenant'],
                403
            );
        }

        return $next($request);
    }

    /**
     * Check if user belongs to the specified tenant.
     *
     * Override this method in a subclass to implement custom logic.
     */
    private function userBelongsToTenant(mixed $user, string $tenantId): bool
    {
        if (is_object($user) && method_exists($user, 'tenants')) {
            $tenants = $user->tenants();

            return is_array($tenants) && in_array($tenantId, $tenants, true);
        }

        if (is_object($user) && method_exists($user, 'tenantId')) {
            return $user->tenantId() === $tenantId;
        }

        if (is_array($user) && isset($user['tenant_id'])) {
            return $user['tenant_id'] === $tenantId;
        }

        return false;
    }
}
