<?php

namespace Modules\Hr\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Hr\Services\EmployeeService;

class EmployeeController
{
    public function __construct(
        protected EmployeeService $service
    ) {}

    public function index(Request $request): Response
    {
        $employees = $this->service->all($request->query());
        return Response::json($employees);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $employee = $this->service->find($id);
        return Response::json($employee);
    }

    public function store(Request $request): Response
    {
        $employee = $this->service->create($request->body());
        return Response::json($employee, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $employee = $this->service->update($id, $request->body());
        return Response::json($employee);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
