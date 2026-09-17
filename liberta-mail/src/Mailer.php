<?php

namespace Liberta\Mail;

class Mailer
{
    public function __construct(
        private SmtpTransport $transport
    ) {}

    public function send(Message $message): bool
    {
        return $this->transport->send($message);
    }

    public function raw(string $to, string $subject, string $body): bool
    {
        $message = new Message();
        $message->to($to)->subject($subject)->body($body);

        return $this->send($message);
    }
}
