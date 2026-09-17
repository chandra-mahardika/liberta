<?php

namespace Modules\Purchasing\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Purchasing\Services\PurchaseReceiptService;

class PurchaseReceiptController
{
    public function __construct(
        protected PurchaseReceiptService $service
    ) {}

    public function index(Request $request): Response
    {
        $receipts = $this->service->all($request->query());
        return Response::json($receipts);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $receipt = $this->service->find($id);
        return Response::json($receipt);
    }

    public function store(Request $request): Response
    {
        $receipt = $this->service->create($request->body());
        return Response::json($receipt, 201);
    }
}
