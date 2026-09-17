<?php

namespace Liberta\Router;

use Closure;

/**
 * Interface for self-contained business modules.
 *
 * Each module provides its own routes, middleware, and configuration.
 * The framework does not scan or auto-discover modules — they are
 * registered explicitly in the application bootstrap.
 *
 * Example:
 *
 *   class InventoryModule implements Module
 *   {
 *       public function name(): string { return 'inventory'; }
 *
 *       public function routes(Router $router): void
 *       {
 *           $router->group(['prefix' => '/inventory'], function ($router) {
 *               $router->get('/products', [ProductController::class, 'index']);
 *               $router->post('/products', [ProductController::class, 'store']);
 *           });
 *       }
 *
 *       public function middleware(): array
 *       {
 *           return ['auth', 'rbac'];
 *       }
 *   }
 *
 *   $router->registerModule(new InventoryModule());
 */
interface Module
{
    /**
     * Unique module name (used for identification, not URL).
     */
    public function name(): string;

    /**
     * Register module routes on the given Router.
     */
    public function routes(Router $router): void;

    /**
     * Global middleware applied to all routes in this module.
     * Return middleware aliases (strings) that map to the container.
     *
     * @return array<int, string>
     */
    public function middleware(): array;
}
