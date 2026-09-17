<?php

namespace Liberta\OAuth;

use Liberta\SqlBuilder\DB;

class TokenRepository
{
    private DB $db;

    public function __construct(?DB $db = null)
    {
        $this->db = $db ?? DB::getInstance();
    }

    public function saveAuthorizationCode(AuthorizationCode $code): void
    {
        $this->db->table('oauth_authorization_codes')->insert([
            'code' => $code->code,
            'client_id' => $code->clientId,
            'redirect_uri' => $code->redirectUri,
            'scopes' => json_encode($code->scopes),
            'expires_at' => $code->expiresAt->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function findAuthorizationCode(string $code): ?array
    {
        return $this->db->table('oauth_authorization_codes')
            ->where('code', '=', $code)
            ->first();
    }

    public function deleteAuthorizationCode(string $code): bool
    {
        return $this->db->table('oauth_authorization_codes')
            ->where('code', '=', $code)
            ->delete();
    }

    public function saveAccessToken(AccessToken $token): void
    {
        $this->db->table('oauth_access_tokens')->insert([
            'token' => $token->token,
            'client_id' => $token->clientId,
            'scopes' => json_encode($token->scopes),
            'expires_at' => $token->expiresAt->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function findAccessToken(string $token): ?array
    {
        return $this->db->table('oauth_access_tokens')
            ->where('token', '=', $token)
            ->first();
    }

    public function deleteAccessToken(string $token): bool
    {
        return $this->db->table('oauth_access_tokens')
            ->where('token', '=', $token)
            ->delete();
    }

    public function revokeAllTokens(string $clientId): bool
    {
        return $this->db->table('oauth_access_tokens')
            ->where('client_id', '=', $clientId)
            ->delete();
    }
}
