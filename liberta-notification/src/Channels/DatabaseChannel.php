<?php

namespace Liberta\Notification\Channels;

use Liberta\Notification\Channel;
use Liberta\Notification\Notifiable;
use Liberta\Notification\Notification;
use Liberta\SqlBuilder\DB;

class DatabaseChannel implements Channel
{
    public function __construct(
        private DB $db,
        private string $table = 'notifications'
    ) {}

    public function send(Notifiable $notifiable, Notification $notification): void
    {
        $data = [
            'type' => get_class($notification),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
            'data' => json_encode($notification->toArray($notifiable)),
            'read_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table($this->table)->insert($data);
    }
}
