<?php

namespace App\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Tenant\TenantContext;

class HealthController
{
    public function check(Request $request): Response
    {
        $tenantId = TenantContext::tenantId();

        return Response::json([
            'status'  => 'ok',
            'service' => 'business-service',
            'tenant'  => $tenantId ?? 'default',
        ]);
    }
}
