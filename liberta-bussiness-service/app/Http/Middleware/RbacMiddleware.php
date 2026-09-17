<?php

namespace App\Http\Middleware;

use Closure;
use Liberta\Router\Middleware\MiddlewareInterface;
use Liberta\Rbac\Contracts\AuthClientInterface;
use Liberta\Rbac\Exceptions\ForbiddenException;

class RbacMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected AuthClientInterface $auth
    ) {}

    public function handle($request, Closure $next)
    {
        $token = $request->headers()['Authorization'] ?? null;

        if ($token !== null) {
            $token = preg_replace('/^Bearer\s+/i', '', $token);
        }

        $user = $this->auth->authenticate($token);

        if (!$user->hasPermission(
            $request->routePermission()
        )) {
            throw new ForbiddenException();
        }

        return $next(
            $request->withUser($user)
        );
    }
}
