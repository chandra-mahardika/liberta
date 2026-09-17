# liberta-event

> Simple event dispatcher with wildcard support.

## Installation

```bash
composer require chandra/liberta-event
```

## Usage

### Basic Usage

```php
use Liberta\Event\EventDispatcher;
use Liberta\Event\Event;

$dispatcher = new EventDispatcher();

// Listen to event
$dispatcher->listen('user.created', function (Event $event) {
    $user = $event->data['user'];
    // Send welcome email
});

// Dispatch event
$dispatcher->dispatch('user.created', ['user' => $user]);
```

### Wildcard Listeners

```php
// Listen to all user.* events
$dispatcher->listen('user.*', function (Event $event) {
    echo "User event: " . $event->name;
});

$dispatcher->dispatch('user.created', ['user' => $user]);
$dispatcher->dispatch('user.updated', ['user' => $user]);
```

### Stop Propagation

```php
$dispatcher->listen('order.processing', function (Event $event) {
    if (!$event->data['valid']) {
        $event->stopPropagation();
        return;
    }
    // Process order
});
```

### Event Object

```php
class OrderEvent extends Event
{
    public function __construct(
        public readonly array $order
    ) {
        parent::__construct('order.created', $order);
    }
}
```

## API

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `listen()` | `string $event, callable $callback` | `void` | Register listener |
| `dispatch()` | `string $event, array $data = []` | `Event` | Dispatch event |
| `removeListener()` | `string $event, callable $callback` | `void` | Remove listener |
| `getListeners()` | `string $event` | `array` | Get all listeners for event |
