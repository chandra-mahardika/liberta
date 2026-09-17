<?php

namespace Modules\Purchasing\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Purchasing\Services\PurchaseInvoiceService;

class PurchaseInvoiceController
{
    public function __construct(
        protected PurchaseInvoiceService $service
    ) {}

    public function index(Request $request): Response
    {
        $invoices = $this->service->all($request->query());
        return Response::json($invoices);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $invoice = $this->service->find($id);
        return Response::json($invoice);
    }

    public function store(Request $request): Response
    {
        $invoice = $this->service->create($request->body());
        return Response::json($invoice, 201);
    }
}
