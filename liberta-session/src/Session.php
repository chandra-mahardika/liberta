<?php

namespace Liberta\Session;

class Session
{
    private array $data = [];
    private bool $started = false;

    public function __construct(
        protected ?string $name = null,
        protected int $lifetime = 7200,
        protected string $path = '/',
        protected ?string $domain = null,
        protected bool $secure = true,
        protected bool $httpOnly = true,
        protected string $sameSite = 'Lax',
    ) {}

    /**
     * Start the session (if not already started).
     */
    public function start(): void
    {
        if ($this->started) {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->data = $_SESSION;
            $this->started = true;
            return;
        }

        if ($this->name !== null) {
            session_name($this->name);
        }

        session_set_cookie_params([
            'lifetime' => $this->lifetime,
            'path'     => $this->path,
            'domain'   => $this->domain ?? '',
            'secure'   => $this->secure,
            'httponly'  => $this->httpOnly,
            'samesite' => $this->sameSite,
        ]);

        session_start();
        $this->data = $_SESSION;
        $this->started = true;
    }

    /**
     * Get a value from the session.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a value in the session.
     */
    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $this->data[$key] = $value;
        $_SESSION[$key] = $value;
    }

    /**
     * Check if a key exists.
     */
    public function has(string $key): bool
    {
        $this->ensureStarted();
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove a key from the session.
     */
    public function remove(string $key): void
    {
        $this->ensureStarted();
        unset($this->data[$key], $_SESSION[$key]);
    }

    /**
     * Flash a value for the next request only.
     */
    public function flash(string $key, mixed $value): void
    {
        $this->set('_flash.' . $key, $value);
    }

    /**
     * Get and clear a flashed value.
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $this->get('_flash.' . $key, $default);
        $this->remove('_flash.' . $key);
        return $value;
    }

    /**
     * Get all session data.
     */
    public function all(): array
    {
        $this->ensureStarted();
        return $this->data;
    }

    /**
     * Clear all session data.
     */
    public function clear(): void
    {
        $this->ensureStarted();
        $this->data = [];
        $_SESSION = [];
    }

    /**
     * Destroy the session completely.
     */
    public function destroy(): void
    {
        $this->data = [];
        $_SESSION = [];

        if ($this->started && session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->started = false;
    }

    /**
     * Get the session ID.
     */
    public function id(): string
    {
        $this->ensureStarted();
        return session_id();
    }

    /**
     * Regenerate the session ID (security: prevent session fixation).
     */
    public function regenerate(): void
    {
        $this->ensureStarted();
        session_regenerate_id(true);
    }

    private function ensureStarted(): void
    {
        if (!$this->started) {
            $this->start();
        }
    }
}
