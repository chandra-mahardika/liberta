<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Domain\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

class TokenService
{
    public function __construct(
        protected UserRepository $users,
        protected string $secret,
        protected string $algorithm = 'HS256',
    ) {}

    public function validate(string $token): User
    {
        try {
            $payload = JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) {
            throw new RuntimeException('Invalid token: ' . $e->getMessage(), 401);
        }

        $userId = $payload->sub ?? null;

        if (!$userId) {
            throw new RuntimeException('Token missing subject claim', 401);
        }

        $user = $this->users->findById($userId);

        if (!$user) {
            throw new RuntimeException('User not found', 401);
        }

        return $user;
    }

    public function create(string $userId, int $ttlSeconds = 3600): string
    {
        $now = time();

        $payload = [
            'iss' => 'liberta-auth',
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }
}
