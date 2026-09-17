<?php

namespace Modules\Purchasing\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Purchasing\Services\SupplierService;

class SupplierController
{
    public function __construct(
        protected SupplierService $service
    ) {}

    public function index(Request $request): Response
    {
        $suppliers = $this->service->all($request->query());
        return Response::json($suppliers);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $supplier = $this->service->find($id);
        return Response::json($supplier);
    }

    public function store(Request $request): Response
    {
        $supplier = $this->service->create($request->body());
        return Response::json($supplier, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $supplier = $this->service->update($id, $request->body());
        return Response::json($supplier);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
