<?php

namespace Modules\Hr\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Modules\Hr\Services\PayrollService;

class PayrollController
{
    public function __construct(
        protected PayrollService $service
    ) {}

    public function index(Request $request): Response
    {
        $payrolls = $this->service->all($request->query());
        return Response::json($payrolls);
    }

    public function show(Request $request): Response
    {
        $id = $request->params()['id'] ?? null;
        $payroll = $this->service->find($id);
        return Response::json($payroll);
    }

    public function store(Request $request): Response
    {
        $payroll = $this->service->create($request->body());
        return Response::json($payroll, 201);
    }
}
