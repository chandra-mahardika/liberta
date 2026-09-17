<?php

namespace Modules\Inventory\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Inventory\Services\WarehouseService;

class WarehouseController
{
    public function __construct(
        protected WarehouseService $service
    ) {}

    public function index(Request $request): Response
    {
        $warehouses = $this->service->all($request->query());
        return Response::json($warehouses);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $warehouse = $this->service->find($id);
        return Response::json($warehouse);
    }

    public function store(Request $request): Response
    {
        $warehouse = $this->service->create($request->body());
        return Response::json($warehouse, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $warehouse = $this->service->update($id, $request->body());
        return Response::json($warehouse);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
