<?php

namespace Modules\Accounting\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Accounting\Services\BalanceSheetService;

class BalanceSheetController
{
    public function __construct(
        protected BalanceSheetService $service
    ) {}

    public function index(Request $request): Response
    {
        $data = $this->service->generate($request->query());
        return Response::json($data);
    }
}
