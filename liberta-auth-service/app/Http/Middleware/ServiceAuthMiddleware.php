<?php

namespace App\Http\Middleware;

use Closure;
use Liberta\Router\Middleware\MiddlewareInterface;
use Liberta\Http\Request;
use Liberta\Rbac\Exceptions\UnauthorizedException;

class ServiceAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected string $serviceKey
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $key = $request->headers()['X-Service-Key'] ?? null;

        if ($key === null || !hash_equals($this->serviceKey, $key)) {
            throw new UnauthorizedException('Invalid service key');
        }

        return $next($request);
    }
}
