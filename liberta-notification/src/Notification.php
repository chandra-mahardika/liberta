<?php

namespace Liberta\Notification;

interface Notification
{
    public function via(Notifiable $notifiable): array;
    public function toMail(Notifiable $notifiable): \Liberta\Mail\Mailable|null;
    public function toArray(Notifiable $notifiable): array;
}
