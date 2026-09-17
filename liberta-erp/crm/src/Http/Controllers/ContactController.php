<?php

namespace Modules\Crm\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Crm\Services\ContactService;

class ContactController
{
    public function __construct(
        protected ContactService $service
    ) {}

    public function index(Request $request): Response
    {
        $contacts = $this->service->all($request->query());
        return Response::json($contacts);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $contact = $this->service->find($id);
        return Response::json($contact);
    }

    public function store(Request $request): Response
    {
        $contact = $this->service->create($request->body());
        return Response::json($contact, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $contact = $this->service->update($id, $request->body());
        return Response::json($contact);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
