<?php

namespace Modules\Purchasing;

use Liberta\Router\Module;
use Liberta\Router\Router;

class PurchasingModule implements Module
{
    public function name(): string
    {
        return 'purchasing';
    }

    public function routes(Router $router): void
    {
        $router->group(['prefix' => '/purchasing'], function (Router $router) {
            $router->get('/suppliers', [\Modules\Purchasing\Http\Controllers\SupplierController::class, 'index']);
            $router->get('/suppliers/{id}', [\Modules\Purchasing\Http\Controllers\SupplierController::class, 'show']);
            $router->post('/suppliers', [\Modules\Purchasing\Http\Controllers\SupplierController::class, 'store']);
            $router->put('/suppliers/{id}', [\Modules\Purchasing\Http\Controllers\SupplierController::class, 'update']);
            $router->delete('/suppliers/{id}', [\Modules\Purchasing\Http\Controllers\SupplierController::class, 'destroy']);

            $router->get('/purchase-orders', [\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'index']);
            $router->get('/purchase-orders/{id}', [\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'show']);
            $router->post('/purchase-orders', [\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'store']);
            $router->put('/purchase-orders/{id}', [\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'update']);
            $router->delete('/purchase-orders/{id}', [\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'destroy']);

            $router->get('/purchase-receipts', [\Modules\Purchasing\Http\Controllers\PurchaseReceiptController::class, 'index']);
            $router->get('/purchase-receipts/{id}', [\Modules\Purchasing\Http\Controllers\PurchaseReceiptController::class, 'show']);
            $router->post('/purchase-receipts', [\Modules\Purchasing\Http\Controllers\PurchaseReceiptController::class, 'store']);

            $router->get('/purchase-invoices', [\Modules\Purchasing\Http\Controllers\PurchaseInvoiceController::class, 'index']);
            $router->get('/purchase-invoices/{id}', [\Modules\Purchasing\Http\Controllers\PurchaseInvoiceController::class, 'show']);
            $router->post('/purchase-invoices', [\Modules\Purchasing\Http\Controllers\PurchaseInvoiceController::class, 'store']);
        });
    }

    public function middleware(): array
    {
        return ['auth', 'rbac'];
    }
}
