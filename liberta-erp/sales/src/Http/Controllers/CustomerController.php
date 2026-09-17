<?php

namespace Modules\Sales\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Sales\Services\CustomerService;

class CustomerController
{
    public function __construct(
        protected CustomerService $service
    ) {}

    public function index(Request $request): Response
    {
        $customers = $this->service->all($request->query());
        return Response::json($customers);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $customer = $this->service->find($id);
        return Response::json($customer);
    }

    public function store(Request $request): Response
    {
        $customer = $this->service->create($request->body());
        return Response::json($customer, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $customer = $this->service->update($id, $request->body());
        return Response::json($customer);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
