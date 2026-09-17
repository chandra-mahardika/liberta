<?php

namespace Liberta\WebSocket;

class Server
{
    /** @var Connection[] */
    private array $connections = [];
    private $master = null;
    private bool $running = false;
    private MessageHandler $handler;

    public function __construct(
        private string $host = '0.0.0.0',
        private int $port = 8080
    ) {}

    public function handle(MessageHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function start(): void
    {
        $this->master = stream_socket_server("tcp://{$this->host}:{$this->port}", $errno, $errstr);

        if (!$this->master) {
            throw new \RuntimeException("Failed to bind: {$errstr} ({$errno})");
        }

        $this->running = true;

        while ($this->running) {
            $read = array_merge([$this->master], array_map(fn($c) => $c->getSocket(), $this->connections));
            $write = $except = null;

            @stream_select($read, $write, $except, 0, 200000);

            foreach ($read as $socket) {
                if ($socket === $this->master) {
                    $this->acceptConnection();
                } else {
                    $this->readFromSocket($socket);
                }
            }
        }
    }

    public function stop(): void
    {
        $this->running = false;

        foreach ($this->connections as $conn) {
            $conn->close();
        }

        if ($this->master) {
            fclose($this->master);
        }
    }

    public function broadcast(string $data): void
    {
        foreach ($this->connections as $conn) {
            $conn->send($data);
        }
    }

    public function getConnectionCount(): int
    {
        return count($this->connections);
    }

    private function acceptConnection(): void
    {
        $socket = @stream_socket_accept($this->master, -1);

        if ($socket === false) {
            return;
        }

        $header = $this->doHandshake($socket);

        if ($header === null) {
            fclose($socket);
            return;
        }

        $connection = new Connection($socket);
        $this->connections[(int) $socket] = $connection;

        stream_set_timeout($socket, 0);
        $this->handler->onOpen($connection);
    }

    private function readFromSocket($socket): void
    {
        $key = (int) $socket;
        $conn = $this->connections[$key] ?? null;

        if ($conn === null) {
            return;
        }

        $data = @fread($socket, 8192);

        if ($data === '' || $data === false) {
            $this->handler->onClose($conn);
            unset($this->connections[$key]);
            fclose($socket);
            return;
        }

        $decoded = $this->decodeFrame($data);

        if ($decoded !== null) {
            $this->handler->onMessage($conn, $decoded);
        }
    }

    private function doHandshake($socket): ?string
    {
        $headers = [];
        $input = fgets($socket);

        if ($input === false || !str_contains($input, 'HTTP/')) {
            return null;
        }

        while (($line = fgets($socket)) !== false && $line !== "\r\n") {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $headers[trim($parts[0])] = trim($parts[1]);
            }
        }

        $key = $headers['Sec-WebSocket-Key'] ?? null;

        if ($key === null) {
            return null;
        }

        $acceptKey = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $response = "HTTP/1.1 101 Switching Protocols\r\n"
            . "Upgrade: websocket\r\n"
            . "Connection: Upgrade\r\n"
            . "Sec-WebSocket-Accept: {$acceptKey}\r\n\r\n";

        fwrite($socket, $response);

        return $headers['Sec-WebSocket-Protocol'] ?? null;
    }

    private function decodeFrame(string $data): ?string
    {
        $length = ord($data[1]) & 127;

        if ($length === 126) {
            $bytes = unpack('n', substr($data, 2, 2));
            $length = $bytes[1];
            $offset = 4;
        } elseif ($length === 127) {
            $bytes = unpack('N2', substr($data, 2, 8));
            $length = ($bytes[1] << 32) | $bytes[2];
            $offset = 10;
        } else {
            $offset = 2;
        }

        if (strlen($data) < $offset + $length) {
            return null;
        }

        return substr($data, $offset, $length);
    }
}
