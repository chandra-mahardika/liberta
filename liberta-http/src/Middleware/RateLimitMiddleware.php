<?php

declare(strict_types=1);

namespace Liberta\Http\Middleware;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Router\Middleware\MiddlewareInterface;
use Closure;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(
        private int $maxAttempts = 60,
        private int $windowSeconds = 60,
        private string $storagePath = '/tmp/liberta_rate_limit',
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveKey($request);
        $now = time();
        $windowStart = $now - $this->windowSeconds;

        $attempts = $this->getAttempts($key, $windowStart);

        if ($attempts >= $this->maxAttempts) {
            $retryAfter = $this->getRetryAfter($key, $windowStart);

            return (new Response(
                ['success' => false, 'message' => 'Too Many Requests'],
                429
            ))->withHeader('Retry-After', (string) $retryAfter)
              ->withHeader('X-RateLimit-Limit', (string) $this->maxAttempts)
              ->withHeader('X-RateLimit-Remaining', '0')
              ->withHeader('X-RateLimit-Reset', (string) ($now + $retryAfter));
        }

        $this->incrementAttempts($key, $now);

        /** @var Response $response */
        $response = $next($request);

        $remaining = max(0, $this->maxAttempts - $attempts - 1);

        return $response
            ->withHeader('X-RateLimit-Limit', (string) $this->maxAttempts)
            ->withHeader('X-RateLimit-Remaining', (string) $remaining)
            ->withHeader('X-RateLimit-Reset', (string) ($now + $this->windowSeconds));
    }

    private function resolveKey(Request $request): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $path = parse_url($request->uri(), PHP_URL_PATH) ?? '/';

        return $ip . ':' . $path;
    }

    private function getAttempts(string $key, int $windowStart): int
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return 0;
        }

        $data = $this->readStorage($file);
        $attempts = 0;

        foreach ($data as $timestamp) {
            if ($timestamp >= $windowStart) {
                $attempts++;
            }
        }

        return $attempts;
    }

    private function incrementAttempts(string $key, int $now): void
    {
        $file = $this->getFilePath($key);
        $data = $this->readStorage($file);
        $data[] = $now;

        $this->writeStorage($file, $data);
    }

    private function getRetryAfter(string $key, int $windowStart): int
    {
        $file = $this->getFilePath($key);
        $data = $this->readStorage($file);

        $oldest = $now = time();
        foreach ($data as $timestamp) {
            if ($timestamp >= $windowStart && $timestamp < $oldest) {
                $oldest = $timestamp;
            }
        }

        return max(1, $oldest + $this->windowSeconds - $now);
    }

    private function getFilePath(string $key): string
    {
        return $this->storagePath . '/' . md5($key) . '.json';
    }

    /** @return list<int> */
    private function readStorage(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);

        if ($content === false) {
            return [];
        }

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param list<int> $data
     */
    private function writeStorage(string $file, array $data): void
    {
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $windowStart = time() - $this->windowSeconds;
        $data = array_values(array_filter(
            $data,
            fn(int $ts): bool => $ts >= $windowStart
        ));

        file_put_contents($file, json_encode($data), LOCK_EX);
    }
}
