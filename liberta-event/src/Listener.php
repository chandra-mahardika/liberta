<?php

namespace Liberta\Event;

interface Listener
{
    /**
     * Handle the event.
     */
    public function handle(Event $event): void;
}
