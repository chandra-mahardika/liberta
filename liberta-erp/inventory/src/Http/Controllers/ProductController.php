<?php

namespace Modules\Inventory\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Inventory\Services\ProductService;

class ProductController
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(Request $request): Response
    {
        $products = $this->service->all($request->query());
        return Response::json($products);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $product = $this->service->find($id);
        return Response::json($product);
    }

    public function store(Request $request): Response
    {
        $product = $this->service->create($request->body());
        return Response::json($product, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $product = $this->service->update($id, $request->body());
        return Response::json($product);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
