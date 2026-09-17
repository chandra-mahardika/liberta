<?php

namespace Liberta\Http;

class Response
{
    protected int $status;
    protected array $headers;
    protected mixed $body;

    public function __construct(
        mixed $body = null,
        int $status = 200,
        array $headers = []
    ) {
        $this->body    = $body;
        $this->status  = $status;
        $this->headers = $headers;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        if (is_array($this->body) || is_object($this->body)) {
            header('Content-Type: application/json');
            echo json_encode($this->body);
            return;
        }

        echo $this->body;
    }

    /* ===== immutable modifiers ===== */

    public function withHeader(string $key, string $value): static
    {
        $clone = clone $this;
        $clone->headers[$key] = $value;
        return $clone;
    }

    public function withStatus(int $status): static
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    public function withBody(mixed $body): static
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    /* ===== getters ===== */

    public function status(): int
    {
        return $this->status;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    /* ===== helpers ===== */

    public static function json(array $data, int $status = 200): static
    {
        return new static($data, $status);
    }

    public static function error(string $message, int $status): static
    {
        return new static([
            'success' => false,
            'message' => $message
        ], $status);
    }
}
