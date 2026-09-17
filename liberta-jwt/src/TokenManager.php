<?php

namespace Liberta\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Liberta\Exception\InvalidTokenException;
use Liberta\Exception\TokenExpiredException;

class TokenManager
{
    public function __construct(
        protected string $secret,
        protected string $algorithm = 'HS256',
        protected int $defaultTtl = 3600,
    ) {}

    /**
     * Create a JWT token.
     *
     * @param array<string, mixed> $claims  Additional claims to include
     */
    public function create(string $subject, array $claims = [], ?int $ttl = null): string
    {
        $ttl ??= $this->defaultTtl;
        $now = time();

        $payload = array_merge([
            'iss' => 'liberta',
            'sub' => $subject,
            'iat' => $now,
            'exp' => $now + $ttl,
        ], $claims);

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Decode and validate a JWT token.
     *
     * @return array<string, mixed>  The decoded payload
     */
    public function decode(string $token): array
    {
        try {
            $payload = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return (array) $payload;
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new TokenExpiredException('Token has expired', 0, $e);
        } catch (\Exception $e) {
            throw new InvalidTokenException('Invalid token: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get the subject (sub claim) from a token with full validation.
     * This method now validates the token signature before extracting the subject.
     */
    public function getSubject(string $token): ?string
    {
        try {
            $payload = $this->decode($token);
            return $payload['sub'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @deprecated Use getSubject() instead which validates the token signature.
     * This method extracts the subject WITHOUT signature verification.
     * Only use for quick inspection of untrusted tokens.
     */
    public function getSubjectUnsafe(string $token): ?string
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);

        return $payload['sub'] ?? null;
    }

    /**
     * Check if a token is expired without throwing.
     */
    public function isExpired(string $token): bool
    {
        try {
            $payload = $this->decode($token);
            return ($payload['exp'] ?? 0) < time();
        } catch (\Throwable) {
            return true;
        }
    }
}
