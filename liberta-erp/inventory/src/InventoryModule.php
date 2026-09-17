<?php

namespace Modules\Inventory;

use Liberta\Router\Module;
use Liberta\Router\Router;

class InventoryModule implements Module
{
    public function name(): string
    {
        return 'inventory';
    }

    public function routes(Router $router): void
    {
        $router->group(['prefix' => '/inventory'], function (Router $router) {
            $router->get('/products', [\Modules\Inventory\Http\Controllers\ProductController::class, 'index']);
            $router->get('/products/{id}', [\Modules\Inventory\Http\Controllers\ProductController::class, 'show']);
            $router->post('/products', [\Modules\Inventory\Http\Controllers\ProductController::class, 'store']);
            $router->put('/products/{id}', [\Modules\Inventory\Http\Controllers\ProductController::class, 'update']);
            $router->delete('/products/{id}', [\Modules\Inventory\Http\Controllers\ProductController::class, 'destroy']);

            $router->get('/categories', [\Modules\Inventory\Http\Controllers\CategoryController::class, 'index']);
            $router->get('/categories/{id}', [\Modules\Inventory\Http\Controllers\CategoryController::class, 'show']);
            $router->post('/categories', [\Modules\Inventory\Http\Controllers\CategoryController::class, 'store']);
            $router->put('/categories/{id}', [\Modules\Inventory\Http\Controllers\CategoryController::class, 'update']);
            $router->delete('/categories/{id}', [\Modules\Inventory\Http\Controllers\CategoryController::class, 'destroy']);

            $router->get('/warehouses', [\Modules\Inventory\Http\Controllers\WarehouseController::class, 'index']);
            $router->get('/warehouses/{id}', [\Modules\Inventory\Http\Controllers\WarehouseController::class, 'show']);
            $router->post('/warehouses', [\Modules\Inventory\Http\Controllers\WarehouseController::class, 'store']);
            $router->put('/warehouses/{id}', [\Modules\Inventory\Http\Controllers\WarehouseController::class, 'update']);
            $router->delete('/warehouses/{id}', [\Modules\Inventory\Http\Controllers\WarehouseController::class, 'destroy']);

            $router->get('/stock-movements', [\Modules\Inventory\Http\Controllers\StockMovementController::class, 'index']);
            $router->get('/stock-movements/{id}', [\Modules\Inventory\Http\Controllers\StockMovementController::class, 'show']);
            $router->post('/stock-movements', [\Modules\Inventory\Http\Controllers\StockMovementController::class, 'store']);

            $router->get('/stock-adjustments', [\Modules\Inventory\Http\Controllers\StockAdjustmentController::class, 'index']);
            $router->get('/stock-adjustments/{id}', [\Modules\Inventory\Http\Controllers\StockAdjustmentController::class, 'show']);
            $router->post('/stock-adjustments', [\Modules\Inventory\Http\Controllers\StockAdjustmentController::class, 'store']);
        });
    }

    public function middleware(): array
    {
        return ['auth', 'rbac'];
    }
}
