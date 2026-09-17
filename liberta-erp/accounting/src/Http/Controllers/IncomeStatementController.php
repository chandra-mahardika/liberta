<?php

namespace Modules\Accounting\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Accounting\Services\IncomeStatementService;

class IncomeStatementController
{
    public function __construct(
        protected IncomeStatementService $service
    ) {}

    public function index(Request $request): Response
    {
        $data = $this->service->generate($request->query());
        return Response::json($data);
    }
}
