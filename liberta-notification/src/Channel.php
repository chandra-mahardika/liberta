<?php

namespace Liberta\Notification;

interface Channel
{
    public function send(Notifiable $notifiable, Notification $notification): void;
}
