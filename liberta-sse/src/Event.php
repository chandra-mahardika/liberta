<?php

namespace Liberta\Sse;

class Event
{
    public function __construct(
        private string $id = '',
        private string $event = 'message',
        private string $data = '',
        private int $retry = 0
    ) {}

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function event(string $event): static
    {
        $this->event = $event;
        return $this;
    }

    public function data(string $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function retry(int $milliseconds): static
    {
        $this->retry = $milliseconds;
        return $this;
    }

    public function json(mixed $data): static
    {
        $this->data = json_encode($data);
        return $this;
    }

    public function __toString(): string
    {
        $output = '';

        if ($this->id !== '') {
            $output .= "id: {$this->id}\n";
        }

        if ($this->event !== 'message') {
            $output .= "event: {$this->event}\n";
        }

        if ($this->retry > 0) {
            $output .= "retry: {$this->retry}\n";
        }

        $lines = explode("\n", $this->data);
        foreach ($lines as $line) {
            $output .= "data: {$line}\n";
        }

        $output .= "\n";

        return $output;
    }
}
