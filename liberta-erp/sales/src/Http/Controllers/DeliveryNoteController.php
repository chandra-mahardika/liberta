<?php

namespace Modules\Sales\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Sales\Services\DeliveryNoteService;

class DeliveryNoteController
{
    public function __construct(
        protected DeliveryNoteService $service
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
}
