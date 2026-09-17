<?php

namespace Liberta\WebSocket;

class Connection
{
    private $socket;
    private bool $closed = false;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function send(string $data): bool
    {
        if ($this->closed) {
            return false;
        }

        $frame = $this->encodeFrame($data);
        return @fwrite($this->socket, $frame) !== false;
    }

    public function sendJson(array $data): bool
    {
        return $this->send(json_encode($data));
    }

    public function close(int $code = 1000, string $reason = ''): void
    {
        if ($this->closed) {
            return;
        }

        $frame = $this->encodeFrame($reason, $code);
        @fwrite($this->socket, $frame);
        $this->closed = true;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function getRemoteAddress(): string
    {
        return stream_socket_get_name($this->socket, true) ?: '';
    }

    private function encodeFrame(string $data, int $opcode = 1): string
    {
        $length = strlen($data);
        $frame = chr(0x80 | $opcode);

        if ($length > 65535) {
            $frame .= chr(127) . pack('NN', ($length >> 32) & 0x7FFFFFFF, $length & 0x7FFFFFFF);
        } elseif ($length > 125) {
            $frame .= chr(255) . pack('n', $length);
        } else {
            $frame .= chr($length);
        }

        return $frame . $data;
    }
}
