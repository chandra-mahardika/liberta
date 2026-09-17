<?php

namespace Liberta\Rbac\Client;

use Liberta\Rbac\Contracts\AuthClientInterface;
use Liberta\Rbac\DTO\User;
use Liberta\Rbac\Exceptions\UnauthorizedException;
use Liberta\Rbac\Exceptions\ForbiddenException;

class HttpAuthClient implements AuthClientInterface
{
    public function __construct(
        protected string $baseUrl,
        protected string $serviceKey,
        protected int $timeout = 5,
    ) {}

    public function authenticate(string $token): User
    {
        if (empty($token)) {
            throw new UnauthorizedException('Token is empty');
        }

        $payload = json_encode(['token' => $token]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => implode("\r\n", [
                    'Content-Type: application/json',
                    'X-Service-Key: ' . $this->serviceKey,
                ]),
                'content' => $payload,
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ]
        ]);

        $raw = @file_get_contents($this->baseUrl . '/validate', false, $context);

        if ($raw === false) {
            throw new UnauthorizedException(
                'Auth service unreachable at ' . $this->baseUrl
            );
        }

        $data = json_decode($raw, true);

        if (!is_array($data)) {
            throw new UnauthorizedException(
                'Invalid response from auth service'
            );
        }

        if (isset($data['success']) && $data['success'] === false) {
            throw new UnauthorizedException(
                $data['message'] ?? 'Authentication failed'
            );
        }

        $required = ['id', 'username', 'roles', 'permissions'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new UnauthorizedException(
                    "Auth service response missing field: {$field}"
                );
            }
        }

        return new User(
            id: (string) $data['id'],
            username: $data['username'],
            roles: (array) $data['roles'],
            permissions: (array) $data['permissions'],
        );
    }

    public function authorize(User $user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }
}
