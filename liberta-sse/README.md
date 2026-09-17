# liberta-sse

> Server-Sent Events for real-time server-to-client streaming.

## Installation

```bash
composer require chandra/liberta-sse
```

## Usage

### Basic SSE

```php
use Liberta\Sse\SseEmitter;
use Liberta\Sse\Event;

$emitter = new SseEmitter(retryMs: 3000);

// Send events
$emitter->send(
    (new Event())->id('1')->event('message')->data('Hello!')
);

// Send JSON
$emitter->json(['users' => $users], 'update', '123');

// Send comment (ignored by client, keeps connection alive)
$emitter->sendComment('heartbeat');

// Check connection
while ($emitter->isConnected()) {
    $emitter->json(['time' => date('H:i:s')]);
    sleep(5);
}
```

### Event Object

```php
$event = (new Event())
    ->id('123')                    // Event ID
    ->event('message')             // Event type
    ->data('Hello!')              // Data
    ->retry(5000);                 // Reconnect interval (ms)

// JSON data
$event = (new Event())->json([
    'type' => 'update',
    'data' => ['count' => 42],
]);

echo $event; // SSE formatted string
```

### Channel System

```php
use Liberta\Sse\Channel;
use Liberta\Sse\SseEmitter;

$channel = new Channel();

// Subscribe
$channel->subscribe($emitter1);
$channel->subscribe($emitter2);

// Broadcast
$channel->broadcast(
    (new Event())->data('Hello everyone!')
);

// Check subscribers
$count = $channel->getSubscriberCount();
```

### Laravel-Style Controller

```php
use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Sse\SseEmitter;

class NotificationController
{
    public function stream(Request $request): Response
    {
        $emitter = new SseEmitter();

        // This would be in a loop/job
        $emitter->json(['message' => 'New notification!']);

        return new Response(); // Headers already sent
    }
}
```

### Client (JavaScript)

```javascript
const eventSource = new EventSource('/notifications');

eventSource.onmessage = (event) => {
    console.log('Data:', event.data);
};

eventSource.addEventListener('update', (event) => {
    console.log('Update:', JSON.parse(event.data));
});

eventSource.onerror = () => {
    console.log('Reconnecting...');
};
```

### Middleware

```php
use Liberta\Sse\SseMiddleware;

// Auto-detect SSE requests
$router->get('/events', [EventController::class, 'stream'])
    ->middleware(new SseMiddleware());
```

## API

### SseEmitter

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `send()` | `Event $event` | `bool` | Send event |
| `json()` | `mixed $data, string $event, string $id` | `bool` | Send JSON event |
| `sendComment()` | `string $comment` | `bool` | Send comment |
| `close()` | — | `void` | Close connection |
| `isConnected()` | — | `bool` | Check connection |

### Event

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `id()` | `string $id` | `static` | Set event ID |
| `event()` | `string $event` | `static` | Set event type |
| `data()` | `string $data` | `static` | Set data |
| `json()` | `mixed $data` | `static` | Set JSON data |
| `retry()` | `int $milliseconds` | `static` | Set retry interval |

### Channel

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `subscribe()` | `SseEmitter $emitter` | `void` | Subscribe emitter |
| `broadcast()` | `Event $event` | `int` | Broadcast to all |
| `getSubscriberCount()` | — | `int` | Count subscribers |
