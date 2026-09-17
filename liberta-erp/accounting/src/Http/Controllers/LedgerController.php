<?php

namespace Modules\Accounting\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Accounting\Services\LedgerService;

class LedgerController
{
    public function __construct(
        protected LedgerService $service
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
}
