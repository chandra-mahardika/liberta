<?php

namespace Liberta\Notification\Messages;

use Liberta\Mail\Message;

class SlackMessage
{
    public function __construct(
        public readonly string $channel = '',
        public readonly string $text = '',
        public readonly string $username = 'Liberta Bot',
        public readonly string $iconEmoji = ':robot_face:'
    ) {}
}
