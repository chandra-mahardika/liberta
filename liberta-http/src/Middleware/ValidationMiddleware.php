<?php

declare(strict_types=1);

namespace Liberta\Http\Middleware;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Http\Validation\Validator;
use Liberta\Router\Middleware\MiddlewareInterface;
use Closure;

class ValidationMiddleware implements MiddlewareInterface
{
    /**
     * @param array<string, string> $rules     Validation rules per field
     * @param array<string, string> $messages  Custom error messages
     * @param string $source  Where to get data: 'body', 'query', or 'all'
     */
    public function __construct(
        private array $rules = [],
        private array $messages = [],
        private string $source = 'body',
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (empty($this->rules)) {
            return $next($request);
        }

        $data = match ($this->source) {
            'body'  => $request->body(),
            'query' => $request->query(),
            'all'   => array_merge($request->query(), $request->body()),
            default => $request->body(),
        };

        $validator = new Validator();

        try {
            $validator->validate($data, $this->rules, $this->messages);
        } catch (\Liberta\Exception\ValidationException $e) {
            return new Response(
                [
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => $e->errors(),
                ],
                422
            );
        }

        return $next($request);
    }
}
