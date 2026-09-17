<?php

namespace Liberta\OAuth;

class AccessToken
{
    public function __construct(
        public readonly string $token,
        public readonly string $clientId,
        public readonly array $scopes,
        public readonly \DateTimeImmutable $expiresAt
    ) {}

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->token,
            'token_type' => 'Bearer',
            'expires_in' => $this->expiresAt->getTimestamp() - time(),
            'scope' => implode(' ', $this->scopes),
        ];
    }
}
