<?php

namespace Modules\Accounting\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Accounting\Services\JournalEntryService;

class JournalEntryController
{
    public function __construct(
        protected JournalEntryService $service
    ) {}

    public function index(Request $request): Response
    {
        $entries = $this->service->all($request->query());
        return Response::json($entries);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $entry = $this->service->find($id);
        return Response::json($entry);
    }

    public function store(Request $request): Response
    {
        $entry = $this->service->create($request->body());
        return Response::json($entry, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $entry = $this->service->update($id, $request->body());
        return Response::json($entry);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
