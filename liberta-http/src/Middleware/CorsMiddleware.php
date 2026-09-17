<?php

declare(strict_types=1);

namespace Liberta\Http\Middleware;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Router\Middleware\MiddlewareInterface;
use Closure;

class CorsMiddleware implements MiddlewareInterface
{
    /** @var list<string> */
    private array $allowedOrigins;

    /** @var list<string> */
    private array $allowedMethods;

    /** @var list<string> */
    private array $allowedHeaders;

    private bool $allowCredentials;

    private int $maxAge;

    /**
     * @param list<string> $allowedOrigins  Use ['*'] to allow all origins
     * @param list<string> $allowedMethods
     * @param list<string> $allowedHeaders
     */
    public function __construct(
        array $allowedOrigins = ['*'],
        array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
        array $allowedHeaders = ['Content-Type', 'Authorization', 'X-Tenant-ID', 'X-Service-Key', 'X-Requested-With'],
        bool $allowCredentials = false,
        int $maxAge = 86400,
    ) {
        $this->allowedOrigins = $allowedOrigins;
        $this->allowedMethods = $allowedMethods;
        $this->allowedHeaders = $allowedHeaders;
        $this->allowCredentials = $allowCredentials;
        $this->maxAge = $maxAge;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if ($request->method() === 'OPTIONS') {
            return $this->handlePreflight($origin);
        }

        /** @var Response $response */
        $response = $next($request);

        $response = $this->addCorsHeaders($response, $origin);

        return $response;
    }

    private function handlePreflight(string $origin): Response
    {
        $response = new Response(null, 204);

        $response = $this->addCorsHeaders($response, $origin);

        $response = $response->withHeader(
            'Access-Control-Allow-Methods',
            implode(', ', $this->allowedMethods)
        );

        $response = $response->withHeader(
            'Access-Control-Allow-Headers',
            implode(', ', $this->allowedHeaders)
        );

        $response = $response->withHeader('Access-Control-Max-Age', (string) $this->maxAge);

        return $response;
    }

    private function addCorsHeaders(Response $response, string $origin): Response
    {
        if (empty($origin)) {
            return $response;
        }

        if (in_array('*', $this->allowedOrigins, true)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        } elseif (in_array($origin, $this->allowedOrigins, true)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
            $response = $response->withHeader('Vary', 'Origin');
        } else {
            return $response;
        }

        if ($this->allowCredentials) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        $response = $response->withHeader(
            'Access-Control-Expose-Headers',
            'X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset, Retry-After'
        );

        return $response;
    }
}
