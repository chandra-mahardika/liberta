# liberta-oauth

> OAuth2 provider and client for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-oauth
```

## Usage

### OAuth2 Client

```php
use Liberta\OAuth\Client;

$client = new Client(
    clientId: 'my-app',
    clientSecret: 'secret',
    redirectUri: 'https://myapp.com/callback',
    authUrl: 'https://auth.example.com/oauth/authorize',
    tokenUrl: 'https://auth.example.com/oauth/token'
);

// Get authorization URL (state should be random and stored in session)
$state = bin2hex(random_bytes(32));
$url = $client->getAuthorizationUrl($state, ['read', 'write']);

// After callback, exchange code for token
$tokens = $client->requestToken($code);
$client->setTokens($tokens['access_token'], $tokens['refresh_token']);

// Refresh token
$tokens = $client->refreshAccessToken();

// Make API calls (throws RuntimeException on cURL errors)
$user = $client->api('GET', 'https://api.example.com/user');
$client->api('POST', 'https://api.example.com/posts', ['title' => 'Hello']);
```

### OAuth2 Provider (Server)

```php
use Liberta\OAuth\OAuthController;
use Liberta\OAuth\ClientRepository;
use Liberta\OAuth\TokenRepository;
use Liberta\Jwt\TokenManager;

$controller = new OAuthController(
    clients: new ClientRepository($db),
    tokens: new TokenRepository($db),
    jwt: new TokenManager(),
    appKey: 'your-app-key'
);

// Routes
$router->get('/oauth/authorize', [$controller, 'authorize']);
$router->post('/oauth/token', [$controller, 'token']);
$router->get('/oauth/verify', [$controller, 'verifyToken']);
```

### Registering Clients

```php
use Liberta\OAuth\ClientRepository;

$repo = new ClientRepository($db);

// Register client
$repo->create([
    'client_id' => 'my-app',
    'client_secret' => password_hash('secret', PASSWORD_DEFAULT),
    'name' => 'My Application',
    'redirect_uri' => 'https://myapp.com/callback',
]);

// Validate
$valid = $repo->validateSecret('my-app', 'secret');
```

### Token Tables Migration

```php
Schema::create('oauth_clients', function ($table) {
    $table->string('client_id')->primary();
    $table->string('client_secret');
    $table->string('name');
    $table->string('redirect_uri');
    $table->timestamps();
});

Schema::create('oauth_authorization_codes', function ($table) {
    $table->string('code')->primary();
    $table->string('client_id');
    $table->string('redirect_uri');
    $table->json('scopes');
    $table->timestamp('expires_at');
    $table->timestamps();
});

Schema::create('oauth_access_tokens', function ($table) {
    $table->string('token')->primary();
    $table->string('client_id');
    $table->json('scopes');
    $table->timestamp('expires_at');
    $table->timestamps();
});
```

## API

### Client

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `getAuthorizationUrl()` | `string $state, array $scopes` | `string` | Get auth URL |
| `requestToken()` | `string $code` | `array` | Exchange code for token |
| `refreshAccessToken()` | — | `array` | Refresh token |
| `api()` | `string $method, string $url, array $data` | `array` | Make API call |
| `setTokens()` | `string $accessToken, string $refreshToken` | `void` | Set tokens |

### OAuthController

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `authorize()` | `Request $request` | `Response` | Handle authorization |
| `token()` | `Request $request` | `Response` | Handle token exchange |
| `verifyToken()` | `Request $request` | `Response` | Verify token |
