<?php

namespace Modules\Sales;

use Liberta\Router\Module;
use Liberta\Router\Router;

class SalesModule implements Module
{
    public function name(): string
    {
        return 'sales';
    }

    public function routes(Router $router): void
    {
        $router->group(['prefix' => '/sales'], function (Router $router) {
            $router->get('/customers', [\Modules\Sales\Http\Controllers\CustomerController::class, 'index']);
            $router->get('/customers/{id}', [\Modules\Sales\Http\Controllers\CustomerController::class, 'show']);
            $router->post('/customers', [\Modules\Sales\Http\Controllers\CustomerController::class, 'store']);
            $router->put('/customers/{id}', [\Modules\Sales\Http\Controllers\CustomerController::class, 'update']);
            $router->delete('/customers/{id}', [\Modules\Sales\Http\Controllers\CustomerController::class, 'destroy']);

            $router->get('/sales-orders', [\Modules\Sales\Http\Controllers\SalesOrderController::class, 'index']);
            $router->get('/sales-orders/{id}', [\Modules\Sales\Http\Controllers\SalesOrderController::class, 'show']);
            $router->post('/sales-orders', [\Modules\Sales\Http\Controllers\SalesOrderController::class, 'store']);
            $router->put('/sales-orders/{id}', [\Modules\Sales\Http\Controllers\SalesOrderController::class, 'update']);
            $router->delete('/sales-orders/{id}', [\Modules\Sales\Http\Controllers\SalesOrderController::class, 'destroy']);

            $router->get('/delivery-notes', [\Modules\Sales\Http\Controllers\DeliveryNoteController::class, 'index']);
            $router->get('/delivery-notes/{id}', [\Modules\Sales\Http\Controllers\DeliveryNoteController::class, 'show']);
            $router->post('/delivery-notes', [\Modules\Sales\Http\Controllers\DeliveryNoteController::class, 'store']);

            $router->get('/sales-invoices', [\Modules\Sales\Http\Controllers\SalesInvoiceController::class, 'index']);
            $router->get('/sales-invoices/{id}', [\Modules\Sales\Http\Controllers\SalesInvoiceController::class, 'show']);
            $router->post('/sales-invoices', [\Modules\Sales\Http\Controllers\SalesInvoiceController::class, 'store']);
        });
    }

    public function middleware(): array
    {
        return ['auth', 'rbac'];
    }
}
