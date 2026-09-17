<?php

namespace Modules\Hr\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Hr\Services\PositionService;

class PositionController
{
    public function __construct(
        protected PositionService $service
    ) {}

    public function index(Request $request): Response
    {
        $positions = $this->service->all($request->query());
        return Response::json($positions);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $position = $this->service->find($id);
        return Response::json($position);
    }

    public function store(Request $request): Response
    {
        $position = $this->service->create($request->body());
        return Response::json($position, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $position = $this->service->update($id, $request->body());
        return Response::json($position);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
