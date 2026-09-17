<?php

namespace Liberta\Sse;

use Liberta\Http\Request;
use Liberta\Http\Response;

class SseMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        $accept = $request->headers()['Accept'] ?? '';

        if (str_contains($accept, 'text/event-stream')) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
        }

        return $next($request);
    }
}
