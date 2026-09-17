<?php

namespace Modules\Sales\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Sales\Services\SalesOrderService;

class SalesOrderController
{
    public function __construct(
        protected SalesOrderService $service
    ) {}

    public function index(Request $request): Response
    {
        $orders = $this->service->all($request->query());
        return Response::json($orders);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $order = $this->service->find($id);
        return Response::json($order);
    }

    public function store(Request $request): Response
    {
        $order = $this->service->create($request->body());
        return Response::json($order, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $order = $this->service->update($id, $request->body());
        return Response::json($order);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
