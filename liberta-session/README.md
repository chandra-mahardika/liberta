# liberta-session

> PHP session wrapper with flash data support.

## Installation

```bash
composer require chandra/liberta-session
```

## Usage

### Basic Usage

```php
use Liberta\Session\Session;

// Start session (secure defaults)
$session = new Session();
$session->start();

// Configure for production
$session = new Session(
    name: 'liberta_session',
    lifetime: 7200,
    secure: true,      // HTTPS only (default: true)
    httpOnly: true,     // No JavaScript access (default: true)
    sameSite: 'Lax'     // CSRF protection (default: 'Lax')
);

// Set values
$session->set('user_id', 123);
$session->set('name', 'Chandra');

// Get values
$userId = $session->get('user_id');
$name = $session->get('name', 'Guest'); // With default

// Check
$session->has('user_id');

// Remove
$session->remove('user_id');

// Flash data (available for next request only)
$session->flash('success', 'User created successfully');
$session->flash('error', 'Something went wrong');

// Get and clear flashed value
$message = $session->getFlash('success');

// Destroy session
$session->destroy();

// Regenerate session ID (prevent session fixation)
$session->regenerate();
```

### Flash Data

```php
$session = Session::start();

// Flash for next request
$session->flash('message', 'Welcome!');

// In next request
$message = $session->get('message'); // 'Welcome!'
$message = $session->get('message'); // null (already consumed)

// Flash input for form validation
$session->flashInput($request->all());
$input = $session->getInput('name');
```

### Session Data

```php
// Get all session data
$all = $session->all();

// Clear all
$session->flush();

// Get session ID
$id = $session->getId();
```

## API

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `start()` | — | `static` | Start session |
| `set()` | `string $key, mixed $value` | `void` | Set value |
| `get()` | `string $key, mixed $default = null` | `mixed` | Get value |
| `has()` | `string $key` | `bool` | Check if exists |
| `forget()` | `string $key` | `void` | Remove value |
| `pull()` | `string $key` | `mixed` | Get and remove |
| `flash()` | `string $key, mixed $value` | `void` | Flash for next request |
| `flashInput()` | `array $data` | `void` | Flash form input |
| `getInput()` | `string $key` | `mixed` | Get flashed input |
| `all()` | — | `array` | Get all data |
| `flush()` | — | `void` | Clear all data |
| `destroy()` | — | `void` | Destroy session |
| `regenerate()` | — | `void` | Regenerate session ID |
| `getId()` | — | `string` | Get session ID |
