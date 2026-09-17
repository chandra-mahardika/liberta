<?php

namespace Modules\Crm\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Crm\Services\OpportunityService;

class OpportunityController
{
    public function __construct(
        protected OpportunityService $service
    ) {}

    public function index(Request $request): Response
    {
        $opportunities = $this->service->all($request->query());
        return Response::json($opportunities);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $opportunity = $this->service->find($id);
        return Response::json($opportunity);
    }

    public function store(Request $request): Response
    {
        $opportunity = $this->service->create($request->body());
        return Response::json($opportunity, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $opportunity = $this->service->update($id, $request->body());
        return Response::json($opportunity);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
