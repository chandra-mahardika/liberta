<?php

namespace Modules\Hr\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Hr\Services\DepartmentService;

class DepartmentController
{
    public function __construct(
        protected DepartmentService $service
    ) {}

    public function index(Request $request): Response
    {
        $departments = $this->service->all($request->query());
        return Response::json($departments);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $department = $this->service->find($id);
        return Response::json($department);
    }

    public function store(Request $request): Response
    {
        $department = $this->service->create($request->body());
        return Response::json($department, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $department = $this->service->update($id, $request->body());
        return Response::json($department);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
