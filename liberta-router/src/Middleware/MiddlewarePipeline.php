<?php

namespace Liberta\Router\Middleware;

use Closure;

class MiddlewarePipeline
{
    protected array $middlewares;
    protected array $container;

    public function __construct(array $middlewares, array $container = [])
    {
        $this->middlewares = $middlewares;
        $this->container   = $container;
    }

    protected function resolve(string $alias): MiddlewareInterface
    {
        $entry = $this->container[$alias] ?? null;

        if ($entry === null) {
            throw new \RuntimeException("Middleware [{$alias}] not found in container");
        }

        if ($entry instanceof \Closure) {
            $resolved = $entry();

            if (!$resolved instanceof MiddlewareInterface) {
                throw new \RuntimeException(
                    "Closure for middleware [{$alias}] must return a MiddlewareInterface instance"
                );
            }

            return $resolved;
        }

        if (is_string($entry)) {
            $resolved = new $entry();

            if (!$resolved instanceof MiddlewareInterface) {
                throw new \RuntimeException(
                    "Class [{$entry}] must implement MiddlewareInterface"
                );
            }

            return $resolved;
        }

        throw new \RuntimeException(
            "Middleware [{$alias}] must be a Closure or class string, got " . get_debug_type($entry)
        );
    }

    public function handle($request, Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $alias) {
                return function ($request) use ($alias, $next) {
                    $middleware = $this->resolve($alias);
                    return $middleware->handle($request, $next);
                };
            },
            $destination
        );

        return $pipeline($request);
    }
}
