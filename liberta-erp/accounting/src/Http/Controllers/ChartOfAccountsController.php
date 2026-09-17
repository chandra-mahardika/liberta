<?php

namespace Modules\Accounting\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Accounting\Services\ChartOfAccountsService;

class ChartOfAccountsController
{
    public function __construct(
        protected ChartOfAccountsService $service
    ) {}

    public function index(Request $request): Response
    {
        $accounts = $this->service->all($request->query());
        return Response::json($accounts);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $account = $this->service->find($id);
        return Response::json($account);
    }

    public function store(Request $request): Response
    {
        $account = $this->service->create($request->body());
        return Response::json($account, 201);
    }

    public function update(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $account = $this->service->update($id, $request->body());
        return Response::json($account);
    }

    public function destroy(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $this->service->delete($id);
        return Response::json(null, 204);
    }
}
