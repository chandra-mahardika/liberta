<?php

namespace Liberta\Notification\Channels;

use Liberta\Notification\Channel;
use Liberta\Notification\Notifiable;
use Liberta\Notification\Notification;
use Liberta\Mail\Mailer;

class MailChannel implements Channel
{
    public function __construct(
        private Mailer $mailer,
        private string $fromAddress = 'noreply@example.com',
        private string $fromName = 'Liberta'
    ) {}

    public function send(Notifiable $notifiable, Notification $notification): void
    {
        $mailable = $notification->toMail($notifiable);

        if ($mailable === null) {
            return;
        }

        $mailable->from($this->fromAddress, $this->fromName);
        $mailable->send($this->mailer);
    }
}
