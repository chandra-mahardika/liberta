<?php

namespace Liberta\Mail;

class Message
{
    private array $headers = [];
    private array $attachments = [];

    public function __construct(
        private string $from = '',
        private string $to = '',
        private string $subject = '',
        private string $body = '',
        private string $htmlBody = '',
    ) {}

    public function from(string $address, ?string $name = null): static
    {
        $this->from = $name !== null ? "{$name} <{$address}>" : $address;
        return $this;
    }

    public function to(string $address, ?string $name = null): static
    {
        $this->to = $name !== null ? "{$name} <{$address}>" : $address;
        return $this;
    }

    public function subject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function body(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function html(string $htmlBody): static
    {
        $this->htmlBody = $htmlBody;
        return $this;
    }

    public function header(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function attach(string $path, ?string $name = null): static
    {
        $this->attachments[] = [
            'path' => $path,
            'name' => $name ?? basename($path),
        ];
        return $this;
    }

    public function getFrom(): string { return $this->from; }
    public function getTo(): string { return $this->to; }
    public function getSubject(): string { return $this->subject; }
    public function getBody(): string { return $this->body; }
    public function getHtmlBody(): string { return $this->htmlBody; }
    public function getHeaders(): array { return $this->headers; }
    public function getAttachments(): array { return $this->attachments; }
}
