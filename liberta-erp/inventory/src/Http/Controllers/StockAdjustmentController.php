<?php

namespace Modules\Inventory\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Inventory\Services\StockAdjustmentService;

class StockAdjustmentController
{
    public function __construct(
        protected StockAdjustmentService $service
    ) {}

    public function index(Request $request): Response
    {
        $adjustments = $this->service->all($request->query());
        return Response::json($adjustments);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $adjustment = $this->service->find($id);
        return Response::json($adjustment);
    }

    public function store(Request $request): Response
    {
        $adjustment = $this->service->create($request->body());
        return Response::json($adjustment, 201);
    }
}
