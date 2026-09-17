<?php

namespace Modules\Inventory\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Inventory\Services\StockMovementService;

class StockMovementController
{
    public function __construct(
        protected StockMovementService $service
    ) {}

    public function index(Request $request): Response
    {
        $movements = $this->service->all($request->query());
        return Response::json($movements);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $movement = $this->service->find($id);
        return Response::json($movement);
    }

    public function store(Request $request): Response
    {
        $movement = $this->service->create($request->body());
        return Response::json($movement, 201);
    }
}
