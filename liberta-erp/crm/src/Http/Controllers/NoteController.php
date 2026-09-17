<?php

namespace Modules\Crm\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Crm\Services\NoteService;

class NoteController
{
    public function __construct(
        protected NoteService $service
    ) {}

    public function index(Request $request): Response
    {
        $notes = $this->service->all($request->query());
        return Response::json($notes);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $note = $this->service->find($id);
        return Response::json($note);
    }

    public function store(Request $request): Response
    {
        $note = $this->service->create($request->body());
        return Response::json($note, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $note = $this->service->update($id, $request->body());
        return Response::json($note);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
