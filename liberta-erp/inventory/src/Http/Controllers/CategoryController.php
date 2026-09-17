<?php

namespace Modules\Inventory\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Inventory\Services\CategoryService;

class CategoryController
{
    public function __construct(
        protected CategoryService $service
    ) {}

    public function index(Request $request): Response
    {
        $categories = $this->service->all($request->query());
        return Response::json($categories);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $category = $this->service->find($id);
        return Response::json($category);
    }

    public function store(Request $request): Response
    {
        $category = $this->service->create($request->body());
        return Response::json($category, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $category = $this->service->update($id, $request->body());
        return Response::json($category);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
