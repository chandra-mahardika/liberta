<?php

declare(strict_types=1);

namespace Liberta\Config;

class Env
{
    private static array $loaded = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                $value = trim($value, '"\'');

                // Convert to appropriate type
                $value = match (strtolower($value)) {
                    'true' => true,
                    'false' => false,
                    'null' => null,
                    default => $value,
                };

                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                self::$loaded[$key] = $value;
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? self::$loaded[$key] ?? $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ?? $default;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    public static function getString(string $key, string $default = ''): string
    {
        return (string) self::get($key, $default);
    }

    public static function required(string $key): mixed
    {
        $value = self::get($key);

        if ($value === null || $value === '') {
            throw new \RuntimeException("Environment variable [{$key}] is required");
        }

        return $value;
    }
}
