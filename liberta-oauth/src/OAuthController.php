<?php

namespace Liberta\OAuth;

use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Jwt\TokenManager;

class OAuthController
{
    public function __construct(
        private ClientRepository $clients,
        private TokenRepository $tokens,
        private TokenManager $jwt,
        private string $appKey
    ) {}

    public function authorize(Request $request): Response
    {
        $clientId = $request->get('client_id');
        $redirectUri = $request->get('redirect_uri');
        $scope = $request->get('scope', 'read');
        $state = $request->get('state');

        if (empty($state) || !is_string($state) || strlen($state) < 16) {
            return (new Response())->withStatus(400)->withBody('Invalid or missing state parameter');
        }

        $client = $this->clients->find($clientId);
        if ($client === null) {
            return (new Response())->withStatus(400)->withBody('Invalid client_id');
        }

        $code = new AuthorizationCode(
            code: bin2hex(random_bytes(32)),
            clientId: $clientId,
            redirectUri: $redirectUri,
            scopes: explode(' ', $scope),
            expiresAt: new \DateTimeImmutable('+10 minutes')
        );

        $this->tokens->saveAuthorizationCode($code);

        $redirectUrl = $redirectUri
            . '?code=' . $code->code
            . '&state=' . urlencode($state);

        return (new Response())->withStatus(302)->withHeader('Location', $redirectUrl);
    }

    public function token(Request $request): Response
    {
        $grantType = $request->get('grant_type');

        return match ($grantType) {
            'authorization_code' => $this->handleAuthorizationCode($request),
            'refresh_token' => $this->handleRefreshToken($request),
            'client_credentials' => $this->handleClientCredentials($request),
            default => (new Response())->withStatus(400)->withBody('Invalid grant_type'),
        };
    }

    private function handleAuthorizationCode(Request $request): Response
    {
        $code = $this->tokens->findAuthorizationCode($request->get('code'));

        if ($code === null) {
            return (new Response())->withStatus(400)->withBody('Invalid code');
        }

        $token = $this->createAccessToken($code['client_id'], json_decode($code['scopes'], true));
        $this->tokens->deleteAuthorizationCode($code['code']);

        return $this->jsonResponse($token->toArray());
    }

    private function handleRefreshToken(Request $request): Response
    {
        $refreshToken = $request->get('refresh_token');

        $payload = $this->jwt->decode($refreshToken, $this->appKey);
        if ($payload === null) {
            return (new Response())->withStatus(401)->withBody('Invalid refresh token');
        }

        $token = $this->createAccessToken($payload['client_id'], $payload['scopes'] ?? ['read']);

        return $this->jsonResponse($token->toArray());
    }

    private function handleClientCredentials(Request $request): Response
    {
        $clientId = $request->get('client_id');
        $clientSecret = $request->get('client_secret');

        if (!$this->clients->validateSecret($clientId, $clientSecret)) {
            return (new Response())->withStatus(401)->withBody('Invalid credentials');
        }

        $token = $this->createAccessToken($clientId, ['read']);

        return $this->jsonResponse($token->toArray());
    }

    public function verifyToken(Request $request): Response
    {
        $authHeader = $request->headers()['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        if ($token === '') {
            return $this->jsonResponse(['active' => false], 401);
        }

        $payload = $this->jwt->decode($token, $this->appKey);

        if ($payload === null) {
            return $this->jsonResponse(['active' => false], 401);
        }

        return $this->jsonResponse([
            'active' => true,
            'client_id' => $payload['client_id'] ?? '',
            'scope' => $payload['scope'] ?? '',
            'exp' => $payload['exp'] ?? 0,
        ]);
    }

    private function createAccessToken(string $clientId, array $scopes): AccessToken
    {
        $token = new AccessToken(
            token: $this->jwt->create(
                ['client_id' => $clientId, 'scopes' => $scopes],
                $this->appKey,
                3600
            ),
            clientId: $clientId,
            scopes: $scopes,
            expiresAt: new \DateTimeImmutable('+1 hour')
        );

        $this->tokens->saveAccessToken($token);
        return $token;
    }

    private function jsonResponse(array $data, int $status = 200): Response
    {
        return (new Response())
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(json_encode($data));
    }
}
