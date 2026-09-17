<?php

namespace Liberta\Sse;

class Channel
{
    /** @var SseEmitter[] */
    private array $subscribers = [];

    public function subscribe(SseEmitter $emitter): void
    {
        $this->subscribers[] = $emitter;
    }

    public function broadcast(Event $event): int
    {
        $count = 0;

        foreach ($this->subscribers as $key => $emitter) {
            if (!$emitter->send($event)) {
                unset($this->subscribers[$key]);
                continue;
            }
            $count++;
        }

        return $count;
    }

    public function getSubscriberCount(): int
    {
        return count($this->subscribers);
    }
}
