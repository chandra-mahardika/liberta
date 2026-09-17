<?php

declare(strict_types=1);

namespace Liberta\Http\Middleware;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\SqlBuilder\Profiler\Profiler;

class PerformanceMiddleware
{
    public function __construct(
        private Profiler $profiler
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $this->profiler->start('http_request');

        $response = $next($request);

        $this->profiler->stop('http_request');

        $report = $this->profiler->getReport();

        $response = $response->withHeader('X-Request-Time', number_format($report['sections']['http_request']['duration_ms'] ?? 0, 2) . 'ms');
        $response = $response->withHeader('X-Query-Count', (string) ($report['queries']['total_queries'] ?? 0));
        $response = $response->withHeader('X-Query-Time', number_format($report['queries']['total_time_ms'] ?? 0, 2) . 'ms');

        return $response;
    }
}
