<?php

namespace Liberta\Container;

/**
 * Lightweight DI container.
 *
 * Supports:
 * - Singleton bindings (one instance shared across all resolves)
 * - Factory bindings (new instance every resolve)
 * - Constructor injection via reflection
 * - Direct value bindings
 *
 * Explicit, no magic. Developer must register what they need.
 *
 * Usage:
 *
 *   $container = new Container();
 *
 *   // Bind instances
 *   $container->instance('db', $db);
 *
 *   // Bind singletons (created once, shared)
 *   $container->singleton(UserService::class, function ($c) {
 *       return new UserService($c->get(UserRepository::class));
 *   });
 *
 *   // Bind factories (new instance every time)
 *   $container->bind(Request::class, function ($c) {
 *       return new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
 *   });
 *
 *   // Resolve
 *   $db = $container->get('db');
 *   $service = $container->get(UserService::class);
 */
class Container
{
    /** @var array<string, mixed> Resolved instances (singletons + instance bindings) */
    private array $instances = [];

    /** @var array<string, callable> Factory definitions */
    private array $bindings = [];

    /** @var array<string, callable> Singleton definitions (lazy) */
    private array $singletons = [];

    /**
     * Bind a value or factory to the container.
     *
     * @param string          $key      Binding key (usually FQCN)
     * @param callable|mixed  $value    Closure that receives Container, or a direct value
     */
    public function bind(string $key, callable|mixed $value): void
    {
        if (is_callable($value) && !is_string($value)) {
            $this->bindings[$key] = $value;
        } else {
            $this->instances[$key] = $value;
        }
    }

    /**
     * Bind a singleton (created once, cached).
     */
    public function singleton(string $key, callable $factory): void
    {
        $this->singletons[$key] = $factory;
    }

    /**
     * Bind a direct value (already constructed).
     */
    public function instance(string $key, mixed $value): void
    {
        $this->instances[$key] = $value;
    }

    /**
     * Resolve a binding from the container.
     *
     * Resolution order:
     * 1. Direct instance
     * 2. Singleton (create + cache)
     * 3. Factory (create new)
     * 4. Try to auto-construct via reflection
     */
    public function get(string $key): mixed
    {
        // 1. Direct instance
        if (array_key_exists($key, $this->instances)) {
            return $this->instances[$key];
        }

        // 2. Singleton
        if (isset($this->singletons[$key])) {
            $this->instances[$key] = $this->singletons[$key]($this);
            unset($this->singletons[$key]);
            return $this->instances[$key];
        }

        // 3. Factory
        if (isset($this->bindings[$key])) {
            return $this->bindings[$key]($this);
        }

        // 4. Auto-construct via reflection
        if (class_exists($key)) {
            return $this->build($key);
        }

        throw new \RuntimeException("Binding [{$key}] not found in container");
    }

    /**
     * Check if a binding exists.
     */
    public function has(string $key): bool
    {
        return isset($this->instances[$key])
            || isset($this->singletons[$key])
            || isset($this->bindings[$key])
            || class_exists($key);
    }

    /**
     * Remove a binding.
     */
    public function forget(string $key): void
    {
        unset($this->instances[$key], $this->singletons[$key], $this->bindings[$key]);
    }

    /**
     * Clear all bindings.
     */
    public function clear(): void
    {
        $this->instances = [];
        $this->bindings = [];
        $this->singletons = [];
    }

    /**
     * Build a class via constructor injection (reflection).
     */
    private function build(string $className): object
    {
        $ref = new \ReflectionClass($className);

        if ($ref->isAbstract() || $ref->isInterface()) {
            throw new \RuntimeException(
                "Cannot auto-construct abstract class or interface: {$className}"
            );
        }

        $constructor = $ref->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $params = $constructor->getParameters();
        $args = [];

        foreach ($params as $param) {
            $type = $param->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = $this->get($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $args[] = null;
            } else {
                throw new \RuntimeException(
                    "Cannot resolve parameter [\$${$param->getName()}] of {$className}::__construct()"
                );
            }
        }

        return $ref->newInstanceArgs($args);
    }
}
