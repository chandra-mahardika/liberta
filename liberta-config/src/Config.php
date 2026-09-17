<?php

namespace Liberta\Config;

class Config
{
    private array $data = [];
    private array $loaded = [];

    public function __construct(
        protected string $basePath
    ) {}

    /**
     * Load a config file. Supports PHP arrays and .env files.
     *
     * @param string $name  Config name (without extension)
     * @param string $group Optional group/filename override
     */
    public function load(string $name, ?string $group = null): void
    {
        $group ??= $name;

        if (isset($this->loaded[$group])) {
            return;
        }

        $file = $this->basePath . '/' . $group . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Config file [{$file}] not found");
        }

        $this->data[$name] = require $file;
        $this->loaded[$group] = true;
    }

    /**
     * Load all config files from a directory.
     */
    public function loadDirectory(string $directory): void
    {
        $path = $this->basePath . '/' . $directory;

        if (!is_dir($path)) {
            throw new \RuntimeException("Config directory [{$path}] not found");
        }

        foreach (glob($path . '/*.php') as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->load($name, $directory . '/' . $name);
        }
    }

    /**
     * Get a config value using dot-notation.
     *
     *   $config->get('database.host')       // 'localhost'
     *   $config->get('database.host', 'x')  // default if not found
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $current = $this->data;

        foreach ($parts as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return $default;
            }
            $current = $current[$part];
        }

        return $current;
    }

    /**
     * Check if a config key exists.
     */
    public function has(string $key): bool
    {
        return $this->get($key, '__NOT_FOUND__') !== '__NOT_FOUND__';
    }

    /**
     * Set a config value (useful for runtime overrides).
     */
    public function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $current = &$this->data;

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
     * Get all config for a group.
     */
    public function group(string $name): array
    {
        return $this->data[$name] ?? [];
    }

    /**
     * Get all config data.
     */
    public function all(): array
    {
        return $this->data;
    }
}
