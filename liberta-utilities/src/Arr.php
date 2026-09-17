<?php

namespace Liberta\Utilities;

class Arr
{
    /**
     * Get an item from an array using dot-notation.
     */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Set an item in an array using dot-notation.
     */
    public static function set(array &$array, string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $current = &$array;

        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $current[$part] = $value;
                break;
            }

            if (!isset($current[$part]) || !is_array($current[$part])) {
                $current[$part] = [];
            }

            $current = &$current[$part];
        }
    }

    /**
     * Check if a key exists using dot-notation.
     */
    public static function has(array $array, string $key): bool
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Remove an item from an array using dot-notation.
     */
    public static function forget(array &$array, string $key): void
    {
        $parts = explode('.', $key);
        $current = &$array;

        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                unset($current[$part]);
                break;
            }

            if (!isset($current[$part]) || !is_array($current[$part])) {
                return;
            }

            $current = &$current[$part];
        }
    }

    /**
     * Get all keys from a nested array using dot-notation prefix.
     *
     * @return string[] Dot-notation keys
     */
    public static function keys(array $array, string $prefix = ''): array
    {
        $keys = [];

        foreach ($array as $key => $value) {
            $fullKey = $prefix !== '' ? "{$prefix}.{$key}" : $key;

            if (is_array($value) && !empty($value)) {
                $keys = array_merge($keys, self::keys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }

        return $keys;
    }

    /**
     * Flatten a multi-dimensional array into dot-notation key => value pairs.
     */
    public static function flatten(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $fullKey = $prefix !== '' ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, self::flatten($value, $fullKey));
            } else {
                $result[$fullKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Only keep the specified keys from an array.
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Remove the specified keys from an array.
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Get a plain array of values for a given key from an array of arrays/objects.
     */
    public static function pluck(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            if (is_array($item)) {
                $result[] = $item[$key] ?? null;
            } elseif (is_object($item)) {
                $result[] = $item->$key ?? null;
            }
        }

        return $result;
    }

    /**
     * Group an array by a key.
     *
     * @return array<string, array>
     */
    public static function groupBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            $groupKey = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);

            if ($groupKey !== null) {
                $result[$groupKey][] = $item;
            }
        }

        return $result;
    }

    /**
     * Sort an array by a key.
     */
    public static function sortBy(array $array, string $key, string $direction = 'asc'): array
    {
        uasort($array, function ($a, $b) use ($key, $direction) {
            $aVal = is_array($a) ? ($a[$key] ?? null) : ($a->$key ?? null);
            $bVal = is_array($b) ? ($b[$key] ?? null) : ($b->$key ?? null);

            $cmp = $aVal <=> $bVal;
            return $direction === 'desc' ? -$cmp : $cmp;
        });

        return $array;
    }

    /**
     * Check if an array is associative (has string keys).
     */
    public static function isAssociative(array $array): bool
    {
        return !array_is_list($array);
    }

    /**
     * Check if an array is empty or null.
     */
    public static function empty(mixed $value): bool
    {
        return $value === null || $value === [] || $value === '';
    }

    /**
     * Get the first element of an array.
     */
    public static function first(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return reset($array) ?: $default;
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last element of an array.
     */
    public static function last(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return end($array) ?: $default;
        }

        $result = $default;

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * Remove all null values from an array.
     */
    public static function withoutNulls(array $array): array
    {
        return array_filter($array, fn ($v) => $v !== null);
    }

    /**
     * Convert an array to a query string.
     */
    public static function query(array $array): string
    {
        return http_build_query($array);
    }

    /**
     * Map a callback over a nested dot-notation structure.
     */
    public static function mapWithKeys(array $array, callable $callback): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $mapped = $callback($value, $key);

            foreach ($mapped as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }
}
