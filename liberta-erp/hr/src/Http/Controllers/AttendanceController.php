<?php

namespace Modules\Hr\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Hr\Services\AttendanceService;

class AttendanceController
{
    public function __construct(
        protected AttendanceService $service
    ) {}

    public function index(Request $request): Response
    {
        $records = $this->service->all($request->query());
        return Response::json($records);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $record = $this->service->find($id);
        return Response::json($record);
    }

    public function store(Request $request): Response
    {
        $record = $this->service->create($request->body());
        return Response::json($record, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $record = $this->service->update($id, $request->body());
        return Response::json($record);
    }
}
