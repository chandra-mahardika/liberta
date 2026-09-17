<?php

namespace Liberta\Mail;

abstract class Mailable
{
    protected string $to = '';
    protected string $from = '';
    protected string $subject = '';
    protected array $data = [];

    abstract protected function build(): Message;

    public function to(string $address, ?string $name = null): static
    {
        $this->to = $name !== null ? "{$name} <{$address}>" : $address;
        return $this;
    }

    public function from(string $address, ?string $name = null): static
    {
        $this->from = $name !== null ? "{$name} <{$address}>" : $address;
        return $this;
    }

    public function subject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function with(array $data): static
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function send(Mailer $mailer): bool
    {
        $message = $this->build();

        if ($this->to !== '') {
            $message->to($this->to);
        }

        if ($this->from !== '') {
            $message->from($this->from);
        }

        if ($this->subject !== '') {
            $message->subject($this->subject);
        }

        return $mailer->send($message);
    }

    protected function html(string $html): string
    {
        foreach ($this->data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', (string) $value, $html);
        }
        return $html;
    }
}
