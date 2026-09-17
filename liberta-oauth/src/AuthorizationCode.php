<?php

namespace Liberta\OAuth;

class AuthorizationCode
{
    public function __construct(
        public readonly string $code,
        public readonly string $clientId,
        public readonly string $redirectUri,
        public readonly array $scopes,
        public readonly \DateTimeImmutable $expiresAt
    ) {}

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }
}
