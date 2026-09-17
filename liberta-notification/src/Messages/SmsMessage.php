<?php

namespace Liberta\Notification\Messages;

class SmsMessage
{
    public function __construct(
        public readonly string $to = '',
        public readonly string $body = ''
    ) {}
}
