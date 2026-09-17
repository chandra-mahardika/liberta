<?php

namespace Liberta\WebSocket;

use Liberta\Http\Request;
use Liberta\Http\Response;

class WebSocketMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        $upgradeHeader = $request->headers()['Upgrade'] ?? '';

        if (strtolower($upgradeHeader) !== 'websocket') {
            return $next($request);
        }

        return (new Response())
            ->withStatus(101)
            ->withHeader('Upgrade', 'websocket')
            ->withHeader('Connection', 'Upgrade');
    }
}
