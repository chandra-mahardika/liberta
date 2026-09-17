<?php

namespace Liberta\Http;

class Request
{
    protected string $method;
    protected string $uri;
    protected array $headers;
    protected array $query;
    protected array $body;
    protected array $params = [];
    protected $user = null;

    public function __construct(
        string $method,
        string $uri,
        array $headers = [],
        array $query = [],
        array $body = []
    ) {
        $this->method  = $method;
        $this->uri     = $uri;
        $this->headers = $headers;
        $this->query   = $query;
        $this->body    = $body;
    }

    public function withParams(array $params): static
    {
        $clone = clone $this;
        $clone->params = $params;
        return $clone;
    }

    public function withUser($user): static
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    // getters
    public function method() { return $this->method; }
    public function uri() { return $this->uri; }
    public function headers() { return $this->headers; }
    public function params() { return $this->params; }
    public function body() { return $this->body; }
    public function query() { return $this->query; }
    public function user() { return $this->user; }
}
