<?php

/**
 * Liberta Router
 * Lightweight, explicit router for native PHP microservices
 * Part of Liberta Ecosystem
 */

namespace Liberta\Router;

use Closure;

class Router
{
    protected array $routes = [];
    protected array $groupStack = [];
    protected array $modules = [];

    /* ======================================================
     | Module Registration
     ====================================================== */

    public function registerModule(Module $module): self
    {
        $name = $module->name();

        if (isset($this->modules[$name])) {
            throw new \RuntimeException("Module [{$name}] is already registered");
        }

        $this->modules[$name] = $module;

        $module->routes($this);

        return $this;
    }

    public function hasModule(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    /* ======================================================
     | Route Registration
     ====================================================== */

    public function get(string $path, Closure|array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, Closure|array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, Closure|array $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, Closure|array $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    protected function addRoute(string $method, string $path, Closure|array $handler): self
    {
        [$prefix, $middlewares] = $this->currentGroup();

        $this->routes[$method][] = [
            'path' => $this->normalize($prefix . $path),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];

        return $this;
    }

    /* ======================================================
     | Route Groups
     ====================================================== */

    public function group(array $options, Closure $callback): void
    {
        $this->groupStack[] = $options;
        $callback($this);
        array_pop($this->groupStack);
    }

    protected function currentGroup(): array
    {
        $prefix = '';
        $middlewares = [];

        foreach ($this->groupStack as $group) {
            $prefix .= $group['prefix'] ?? '';
            $middlewares = array_merge($middlewares, $group['middleware'] ?? []);
        }

        return [$prefix, $middlewares];
    }

    /* ======================================================
     | Dispatch
     ====================================================== */

    public function dispatch(string $method, string $uri)
    {
        $uri = $this->normalize($uri);

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = $this->compile($route['path'], $params);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return [
                    'handler' => $route['handler'],
                    'params' => $matches,
                    'middlewares' => $route['middlewares']
                ];
            }
        }

        return null;
    }

    /* ======================================================
     | Helpers
     ====================================================== */

    protected function compile(string $path, &$params = []): string
    {
        return '#^' . preg_replace('#\{([^/]+)\}#', '([^/]+)', $path) . '$#';
    }

    protected function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }
}
