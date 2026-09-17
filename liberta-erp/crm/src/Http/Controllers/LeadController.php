<?php

namespace Modules\Crm\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Crm\Services\LeadService;

class LeadController
{
    public function __construct(
        protected LeadService $service
    ) {}

    public function index(Request $request): Response
    {
        $leads = $this->service->all($request->query());
        return Response::json($leads);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $lead = $this->service->find($id);
        return Response::json($lead);
    }

    public function store(Request $request): Response
    {
        $lead = $this->service->create($request->body());
        return Response::json($lead, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $lead = $this->service->update($id, $request->body());
        return Response::json($lead);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
