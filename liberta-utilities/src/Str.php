<?php

namespace Liberta\Utilities;

class Str
{
    /**
     * Convert a string to camelCase.
     */
    public static function camel(string $value): string
    {
        return lcfirst(self::studly($value));
    }

    /**
     * Convert a string to StudlyCase (PascalCase).
     */
    public static function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    /**
     * Convert a string to kebab-case.
     */
    public static function kebab(string $value): string
    {
        return str_replace('_', '-', strtolower($value));
    }

    /**
     * Convert a string to snake_case.
     */
    public static function snake(string $value): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($value)));
    }

    /**
     * Convert a string to title case.
     */
    public static function title(string $value): string
    {
        return ucwords(str_replace('-', ' ', str_replace('_', ' ', strtolower($value))));
    }

    /**
     * Check if a string starts with a given prefix.
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }

        foreach ($needles as $needle) {
            if ($needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string ends with a given suffix.
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }

        foreach ($needles as $needle) {
            if ($needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string contains a given substring.
     */
    public static function contains(string $haystack, string|array $needles): bool
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }

        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Limit a string to a given number of characters, appending "..." if truncated.
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit) . $end;
    }

    /**
     * Generate a random string of given length.
     */
    public static function random(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($chars) - 1;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }

        return $result;
    }

    /**
     * Generate a UUID v4.
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Pluralize a word (basic English rules).
     */
    public static function plural(string $value): string
    {
        $rules = [
            '/(s)tatus$/' => '$1tatuses',
            '/(quiz)$/' => '$1zes',
            '/^(ox)$/' => '$1en',
            '/([m|l])ouse$/' => '$1ice',
            '/(matr|vert|ind)ix|ex$/' => '$1ices',
            '/(x|ch|ss|sh)$/' => '$1es',
            '/([^aeiouy]|qu)y$/' => '$1ies',
            '/(?:([^f])fe|([lr])f)$/' => '$1$2ves',
            '/(shea|lea|loa|thie)f$/' => '$1ves',
            '/sis$/' => 'ses',
            '/([ti])um$/' => '$1a',
            '/(corpse)$/' => '$1',
            '/(news)$/' => '$1',
            '/(.*)$/s' => '$1s',
        ];

        foreach ($rules as $pattern => $replacement) {
            if (preg_match($pattern, $value)) {
                return preg_replace($pattern, $replacement, $value);
            }
        }

        return $value . 's';
    }

    /**
     * Singularize a word (basic English rules).
     */
    public static function singular(string $value): string
    {
        $rules = [
            '/(s)tatuses$/' => '$1tatus',
            '/(quiz)zes$/' => '$1',
            '/(matr)ices$/' => '$1ix',
            '/(vert|ind)ices$/' => '$1ex',
            '/^(ox)en$/' => '$1',
            '/([m|l])ice$/' => '$1ouse',
            '/(x|ch|ss|sh)es$/' => '$1',
            '/([^aeiouy]|qu)ies$/' => '$1y',
            '/([lr])ves$/' => '$1f',
            '/(shea|lea|loa|thie)ves$/' => '$1f',
            '/(is)as$/' => '$1',
            '/(ses)$/' => '$1',
            '/(a)es$/' => '$1',
            '/(o)es$/' => '$1',
            '/(shoe|slave)$/' => '$1s',
            '/(.*)s$/' => '$1',
        ];

        foreach ($rules as $pattern => $replacement) {
            if (preg_match($pattern, $value)) {
                return preg_replace($pattern, $replacement, $value);
            }
        }

        return $value;
    }

    /**
     * Slugify a string.
     */
    public static function slug(string $value, string $separator = '-'): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', $separator, $value), $separator));
        return preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $slug);
    }

    /**
     * Check if a string is a valid UUID.
     */
    public static function isUuid(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
    }

    /**
     * Mask a string (e.g., for display: "***@example.com").
     */
    public static function mask(string $value, string $character = '*', int $visibleStart = 0, int $visibleEnd = 0): string
    {
        $length = mb_strlen($value);

        if ($visibleStart + $visibleEnd >= $length) {
            return $value;
        }

        $start = mb_substr($value, 0, $visibleStart);
        $end = mb_substr($value, $length - $visibleEnd);
        $mask = str_repeat($character, $length - $visibleStart - $visibleEnd);

        return $start . $mask . $end;
    }
}
