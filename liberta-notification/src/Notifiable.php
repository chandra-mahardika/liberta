<?php

namespace Liberta\Notification;

interface Notifiable
{
    public function notify(Notification $notification): void;
    public function notificationRoute(string $channel): mixed;
}
