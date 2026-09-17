<?php

namespace Liberta\Router\Middleware;

use Closure;

interface MiddlewareInterface
{
    public function handle($request, Closure $next);
}

