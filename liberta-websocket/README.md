# liberta-websocket

> RFC 6455 WebSocket server for real-time communication.

## Installation

```bash
composer require chandra/liberta-websocket
```

## Usage

### Creating a WebSocket Server

```php
use Liberta\WebSocket\Server;
use Liberta\WebSocket\MessageHandler;
use Liberta\WebSocket\Connection;

class ChatHandler implements MessageHandler
{
    /** @var Connection[] */
    private array $clients = [];

    public function onOpen(Connection $connection): void
    {
        $this->clients[(int) $connection->getSocket()] = $connection;
        $this->broadcast(json_encode([
            'type' => 'system',
            'message' => 'User joined',
        ]));
    }

    public function onMessage(Connection $connection, string $message): void
    {
        $data = json_decode($message, true);

        // Broadcast to all clients
        $this->broadcast(json_encode([
            'type' => 'message',
            'user' => $data['user'] ?? 'anonymous',
            'message' => $data['message'] ?? '',
        ]));
    }

    public function onClose(Connection $connection): void
    {
        unset($this->clients[(int) $connection->getSocket()]);
    }

    public function onError(Connection $connection, \Throwable $e): void
    {
        error_log("WebSocket error: " . $e->getMessage());
    }

    private function broadcast(string $data): void
    {
        foreach ($this->clients as $client) {
            $client->send($data);
        }
    }
}

// Start server
$server = new Server('0.0.0.0', 8080);
$server->handle(new ChatHandler());
$server->start();
```

### Client Connection (JavaScript)

```javascript
const ws = new WebSocket('ws://localhost:8080');

ws.onopen = () => {
    ws.send(JSON.stringify({
        user: 'Chandra',
        message: 'Hello!'
    }));
};

ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log(data);
};

ws.onclose = () => {
    console.log('Disconnected');
};
```

### Connection API

```php
$connection->send('Hello!');           // Send string
$connection->sendJson(['key' => 'val']); // Send JSON
$connection->close(1000, 'Goodbye');    // Close connection
$connection->getRemoteAddress();        // Client IP
$connection->isClosed();                // Check status
```

### Middleware

```php
use Liberta\WebSocket\WebSocketMiddleware;

// HTTP upgrade middleware
$router->get('/ws', [WebSocketMiddleware::class, 'handle']);
```

## API

### Server

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `handle()` | `MessageHandler $handler` | `void` | Set message handler |
| `start()` | — | `void` | Start server (blocks) |
| `stop()` | — | `void` | Stop server |
| `broadcast()` | `string $data` | `void` | Send to all clients |
| `getConnectionCount()` | — | `int` | Connected clients |

### Connection

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `send()` | `string $data` | `bool` | Send data |
| `sendJson()` | `array $data` | `bool` | Send JSON |
| `close()` | `int $code = 1000, string $reason = ''` | `void` | Close connection |
| `getRemoteAddress()` | — | `string` | Client IP |
| `isClosed()` | — | `bool` | Check if closed |

### MessageHandler Interface

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `onOpen()` | `Connection $connection` | `void` | Client connected |
| `onMessage()` | `Connection $connection, string $message` | `void` | Message received |
| `onClose()` | `Connection $connection` | `void` | Client disconnected |
| `onError()` | `Connection $connection, \Throwable $e` | `void` | Error occurred |
