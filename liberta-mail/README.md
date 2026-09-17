# liberta-mail

> SMTP mail transport with mailable class abstraction.

## Installation

```bash
composer require chandra/liberta-mail
```

## Usage

### Basic Email

```php
use Liberta\Mail\Message;
use Liberta\Mail\SmtpTransport;
use Liberta\Mail\Mailer;

$transport = new SmtpTransport(
    host: 'smtp.gmail.com',
    port: 587,
    encryption: 'tls',
    username: 'user@gmail.com',
    password: 'password'
);

$mailer = new Mailer($transport);

$message = new Message();
$message->from('admin@example.com', 'Admin')
        ->to('user@example.com', 'User')
        ->subject('Welcome!')
        ->body('Plain text content')
        ->html('<h1>Welcome to Liberta!</h1>');

$mailer->send($message);
```

### Attachments

```php
$message = new Message();
$message->from('admin@example.com')
        ->to('user@example.com')
        ->subject('Report')
        ->body('Please find attached.')
        ->attach('/tmp/report.pdf', 'report.pdf')
        ->attach('/tmp/data.xlsx');

$mailer->send($message);
```

### Mailable Class

```php
use Liberta\Mail\Mailable;
use Liberta\Mail\Message;

class WelcomeEmail extends Mailable
{
    public function __construct(
        private string $userName,
        private string $activationLink
    ) {}

    protected function build(): Message
    {
        return (new Message())
            ->subject('Welcome to Liberta!')
            ->body("Hello {$this->userName}, please activate your account.")
            ->html($this->html("
                <h1>Welcome, {$this->userName}!</h1>
                <p>Click <a href='{$this->activationLink}'>here</a> to activate.</p>
            "));
    }
}

// Usage
$email = new WelcomeEmail('Chandra', 'https://example.com/activate?token=abc123');
$email->to('chandra.libertania@gmail.com', 'Chandra');
$email->send($mailer);
```

### Quick Send

```php
$mailer->raw('user@example.com', 'Subject', 'Body content');
```

## API

### Message

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `from()` | `string $address, ?string $name` | `static` | Set sender |
| `to()` | `string $address, ?string $name` | `static` | Set recipient |
| `subject()` | `string $subject` | `static` | Set subject |
| `body()` | `string $body` | `static` | Set plain text body |
| `html()` | `string $html` | `static` | Set HTML body |
| `header()` | `string $name, string $value` | `static` | Add header |
| `attach()` | `string $path, ?string $name` | `static` | Attach file |

### SmtpTransport

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `send()` | `Message $message` | `bool` | Send email |

### Mailable

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `to()` | `string $address, ?string $name` | `static` | Set recipient |
| `from()` | `string $address, ?string $name` | `static` | Set sender |
| `subject()` | `string $subject` | `static` | Set subject |
| `with()` | `array $data` | `static` | Pass template data |
| `send()` | `Mailer $mailer` | `bool` | Send mailable |
