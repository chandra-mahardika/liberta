# liberta-notification

> Notification system with mail and database channels.

## Installation

```bash
composer require chandra/liberta-notification
```

## Usage

### Creating Notifications

```php
use Liberta\Notification\Notification;
use Liberta\Notification\Notifiable;

class OrderShipped implements Notification
{
    public function __construct(
        private array $order
    ) {}

    public function via(Notifiable $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(Notifiable $notifiable): ?Mailable
    {
        return (new OrderShippedEmail($this->order))
            ->to($notifiable->notificationRoute('mail'));
    }

    public function toArray(Notifiable $notifiable): array
    {
        return [
            'order_id' => $this->order['id'],
            'message' => 'Your order has been shipped!',
        ];
    }
}
```

### Notifiable Model

```php
use Liberta\Notification\Notifiable;

class User implements Notifiable
{
    public function getKey(): int
    {
        return $this->id;
    }

    public function notify(Notification $notification): void
    {
        $manager = app(NotificationManager::class);
        $manager->send($this, $notification);
    }

    public function notificationRoute(string $channel): mixed
    {
        return match ($channel) {
            'mail' => $this->email,
            'database' => $this->id,
            default => null,
        };
    }
}
```

### Sending Notifications

```php
$notification = new OrderShipped($order);
$user->notify($notification);
```

### Notification Manager

```php
use Liberta\Notification\NotificationManager;
use Liberta\Notification\Channels\MailChannel;
use Liberta\Notification\Channels\DatabaseChannel;

$manager = new NotificationManager();
$manager->channel('mail', new MailChannel($mailer));
$manager->channel('database', new DatabaseChannel($db));

$manager->send($user, $notification);
```

## API

### Notification Interface

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `via()` | `Notifiable $notifiable` | `array` | Channels to use |
| `toMail()` | `Notifiable $notifiable` | `?Mailable` | Mail representation |
| `toArray()` | `Notifiable $notifiable` | `array` | Database array |

### Notifiable Interface

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `notify()` | `Notification $notification` | `void` | Send notification |
| `notificationRoute()` | `string $channel` | `mixed` | Get route for channel |

### NotificationManager

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `channel()` | `string $name, Channel $channel` | `void` | Register channel |
| `send()` | `Notifiable $notifiable, Notification $notification` | `void` | Send notification |
