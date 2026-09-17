<?php

namespace Liberta\Sse;

class SseEmitter
{
    private bool $connected = false;

    public function __construct(
        private int $retryMs = 3000
    ) {}

    public function send(Event $event): bool
    {
        if (!$this->connected) {
            $this->connect();
        }

        if ($event->retry === 0 && $this->retryMs > 0) {
            $event->retry($this->retryMs);
        }

        echo $event;
        ob_flush();
        flush();

        return !connection_aborted();
    }

    public function json(mixed $data, string $event = 'message', string $id = ''): bool
    {
        $e = (new Event())->event($event)->json($data);

        if ($id !== '') {
            $e->id($id);
        }

        return $this->send($e);
    }

    public function sendComment(string $comment): bool
    {
        if (!$this->connected) {
            $this->connect();
        }

        echo ": {$comment}\n\n";
        ob_flush();
        flush();

        return !connection_aborted();
    }

    public function close(): void
    {
        $this->connected = false;
    }

    public function isConnected(): bool
    {
        return $this->connected && !connection_aborted();
    }

    private function connect(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $this->connected = true;
    }
}
