<?php

namespace Modules\Crm\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Crm\Services\ActivityService;

class ActivityController
{
    public function __construct(
        protected ActivityService $service
    ) {}

    public function index(Request $request): Response
    {
        $activities = $this->service->all($request->query());
        return Response::json($activities);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $activity = $this->service->find($id);
        return Response::json($activity);
    }

    public function store(Request $request): Response
    {
        $activity = $this->service->create($request->body());
        return Response::json($activity, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $activity = $this->service->update($id, $request->body());
        return Response::json($activity);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
