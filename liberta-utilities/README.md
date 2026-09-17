# liberta-utilities

> String and array helper functions for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-utilities
```

## Usage

### Str Class

```php
use Liberta\Utilities\Str;

// Case conversion
Str::camel('hello_world');        // 'helloWorld'
Str::studly('hello_world');       // 'HelloWorld'
Str::kebab('helloWorld');         // 'hello-world'
Str::snake('HelloWorld');         // 'hello_world'
Str::title('hello world');        // 'Hello World'
Str::lower('HELLO');              // 'hello'
Str::upper('hello');              // 'HELLO'

// Generate
Str::uuid();                      // '550e8400-e29b-41d4-a716-446655440000'
Str::random(16);                  // Random 16-char string
Str::slug('Hello World!');        // 'hello-world'
Str::orderedUuid();               // Time-ordered UUID

// Manipulation
Str::limit('Long text here', 10); // 'Long te...'
Str::mask('1234567890', '*', 3, 4); // '123****890'
Str::plural('user');              // 'users'
Str::singular('users');           // 'user'

// Search
Str::contains('hello world', 'world'); // true
Str::startsWith('hello', 'hel');       // true
Str::endsWith('hello', 'llo');         // true
Str::studlyContains('HelloWorld', 'hello'); // true
```

### Arr Class

```php
use Liberta\Utilities\Arr;

$data = [
    'user' => [
        'name' => 'Chandra',
        'email' => 'chandra.libertania@gmail.com',
    ],
];

// Dot-notation access
Arr::get($data, 'user.name');              // 'Chandra'
Arr::get($data, 'user.age', 25);           // 25 (default)
Arr::has($data, 'user.email');             // true
Arr::set($data, 'user.age', 25);           // Sets value
Arr::forget($data, 'user.email');          // Removes key

// Array operations
Arr::flatten(['a' => [1, 2], 'b' => [3, 4]]); // [1, 2, 3, 4]
Arr::pluck($data, 'user.name');            // ['Chandra']
Arr::only($data, ['user.name']);            // ['user' => ['name' => 'Chandra']]
Arr::except($data, ['user.email']);         // ['user' => ['name' => 'Chandra']]

// Filtering
Arr::where($data, fn($v) => $v['name'] === 'Chandra');
Arr::first($data, fn($v) => str_contains($v['email'], 'example'));
Arr::last($data);
Arr::pull($data, 'user.name');            // Returns and removes
Arr::pop($data);                          // Returns last element

// Sorting
Arr::sortBy($data, 'user.name');
Arr::groupBy($users, 'department');
```

## API

### Str

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `camel()` | `string $value` | `string` | Convert to camelCase |
| `studly()` | `string $value` | `string` | Convert to StudlyCase |
| `kebab()` | `string $value` | `string` | Convert to kebab-case |
| `snake()` | `string $value` | `string` | Convert to snake_case |
| `title()` | `string $value` | `string` | Convert to Title Case |
| `uuid()` | — | `string` | Generate UUID v4 |
| `random()` | `int $length` | `string` | Generate random string |
| `slug()` | `string $value` | `string` | Generate URL slug |
| `limit()` | `string $value, int $limit` | `string` | Limit string length |
| `contains()` | `string $haystack, string $needle` | `bool` | Check if contains |
| `plural()` | `string $value` | `string` | Pluralize word |
| `singular()` | `string $value` | `string` | Singularize word |

### Arr

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `get()` | `array $array, string $key, mixed $default` | `mixed` | Get by dot-notation |
| `set()` | `array &$array, string $key, mixed $value` | `void` | Set by dot-notation |
| `has()` | `array $array, string $key` | `bool` | Check if key exists |
| `forget()` | `array &$array, string $key` | `void` | Remove by dot-notation |
| `flatten()` | `array $array` | `array` | Flatten nested array |
| `pluck()` | `array $array, string $key` | `array` | Extract values by key |
| `only()` | `array $array, array $keys` | `array` | Only specified keys |
| `except()` | `array $array, array $keys` | `array` | Except specified keys |
| `first()` | `array $array, callable $callback` | `mixed` | First matching element |
