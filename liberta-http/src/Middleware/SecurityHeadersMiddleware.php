<?php

declare(strict_types=1);

namespace Liberta\Http\Middleware;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Router\Middleware\MiddlewareInterface;
use Closure;

class SecurityHeadersMiddleware implements MiddlewareInterface
{
    /** @var array<string, string> */
    private array $headers;

    /**
     * @param array<string, string> $overrides  Override default header values
     */
    public function __construct(array $overrides = [])
    {
        $defaults = [
            'X-Content-Type-Options'    => 'nosniff',
            'X-Frame-Options'           => 'DENY',
            'X-XSS-Protection'          => '1; mode=block',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin',
            'Permissions-Policy'        => 'camera=(), microphone=(), geolocation=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Cache-Control'             => 'no-store, no-cache, must-revalidate, private',
            'Pragma'                    => 'no-cache',
        ];

        $this->headers = array_merge($defaults, $overrides);
    }

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }
}
