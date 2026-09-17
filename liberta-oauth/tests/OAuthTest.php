<?php

declare(strict_types=1);

namespace Liberta\OAuth\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\OAuth\AccessToken;
use Liberta\OAuth\AuthorizationCode;

class OAuthTest extends TestCase
{
    public function testAuthorizationCodeIsExpired(): void
    {
        $expired = new AuthorizationCode(
            code: 'abc123',
            clientId: 'client1',
            redirectUri: 'http://example.com',
            scopes: ['read'],
            expiresAt: new \DateTimeImmutable('-1 hour')
        );

        $this->assertTrue($expired->isExpired());
    }

    public function testAuthorizationCodeIsNotExpired(): void
    {
        $valid = new AuthorizationCode(
            code: 'abc123',
            clientId: 'client1',
            redirectUri: 'http://example.com',
            scopes: ['read'],
            expiresAt: new \DateTimeImmutable('+1 hour')
        );

        $this->assertFalse($valid->isExpired());
    }

    public function testAccessTokenToArray(): void
    {
        $token = new AccessToken(
            token: 'test_token_123',
            clientId: 'client1',
            scopes: ['read', 'write'],
            expiresAt: new \DateTimeImmutable('+1 hour')
        );

        $array = $token->toArray();

        $this->assertEquals('test_token_123', $array['access_token']);
        $this->assertEquals('Bearer', $array['token_type']);
        $this->assertArrayHasKey('expires_in', $array);
        $this->assertEquals('read write', $array['scope']);
    }

    public function testAccessTokenIsExpired(): void
    {
        $token = new AccessToken(
            token: 'expired',
            clientId: 'client1',
            scopes: ['read'],
            expiresAt: new \DateTimeImmutable('-1 hour')
        );

        $this->assertTrue($token->isExpired());
    }
}
