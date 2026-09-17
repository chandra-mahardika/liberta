<?php

namespace Liberta\Mail;

class SmtpTransport
{
    private $connection = null;

    public function __construct(
        private string $host = 'localhost',
        private int $port = 587,
        private string $encryption = 'tls',
        private ?string $username = null,
        private ?string $password = null,
        private int $timeout = 30
    ) {}

    public function send(Message $message): bool
    {
        $this->connect();

        $this->sendCommand("EHLO " . gethostname());
        $this->handleEncryption();
        $this->authenticate();
        $this->sendCommand("MAIL FROM: <{$this->extractEmail($message->getFrom())}>");

        foreach ([$message->getTo()] as $recipient) {
            $this->sendCommand("RCPT TO: <{$this->extractEmail($recipient)}>");
        }

        $this->sendCommand("DATA");
        $this->sendRaw($this->buildHeaders($message));
        $this->sendRaw("From: {$message->getFrom()}");
        $this->sendRaw("To: {$message->getTo()}");
        $this->sendRaw("Subject: {$message->getSubject()}");
        $this->sendRaw("MIME-Version: 1.0");
        $this->sendRaw("Date: " . date('r'));

        foreach ($message->getHeaders() as $name => $value) {
            $this->sendRaw("{$name}: {$value}");
        }

        if (!empty($message->getAttachments())) {
            $boundary = md5(uniqid());
            $this->sendRaw("Content-Type: multipart/mixed; boundary=\"{$boundary}\"");
            $this->sendRaw("");
            $this->sendBodyPart($boundary, $message);
            $this->sendAttachmentPart($boundary, $message);
            $this->sendRaw("--{$boundary}--");
        } else {
            $this->sendSimpleBody($message);
        }

        $this->sendCommand(".");
        $this->sendCommand("QUIT");
        $this->disconnect();

        return true;
    }

    private function connect(): void
    {
        $host = "tcp://{$this->host}:{$this->stream_port}";
        $this->connection = stream_socket_client(
            $host,
            $errno,
            $errstr,
            $this->timeout
        );

        if (!$this->connection) {
            throw new \RuntimeException("SMTP connection failed: {$errstr} ({$errno})");
        }

        stream_set_timeout($this->connection, $this->timeout);
    }

    private function disconnect(): void
    {
        if (is_resource($this->connection)) {
            fclose($this->connection);
            $this->connection = null;
        }
    }

    private function handleEncryption(): void
    {
        if ($this->encryption === 'ssl') {
            $this->sendCommand("STARTTLS");
            $this->startTls();
            $this->sendCommand("EHLO " . gethostname());
        }
    }

    private function authenticate(): void
    {
        if ($this->username !== null && $this->password !== null) {
            $this->sendCommand("AUTH LOGIN");
            $this->sendCommand(base64_encode($this->username));
            $this->sendCommand(base64_encode($this->password));
        }
    }

    private function sendCommand(string $command): string
    {
        $this->sendRaw($command);
        $response = $this->readResponse();
        $code = (int) substr($response, 0, 3);

        if ($code >= 400) {
            throw new \RuntimeException("SMTP error: {$response}");
        }

        return $response;
    }

    private function sendRaw(string $data): void
    {
        fwrite($this->connection, $data . "\r\n");
    }

    private function readResponse(): string
    {
        $response = '';
        while (($line = fgets($this->connection)) !== false) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return trim($response);
    }

    private function buildHeaders(Message $message): string
    {
        $headers = $message->getHeaders();
        $headerLines = [];

        foreach ($headers as $name => $value) {
            $headerLines[] = "{$name}: {$value}";
        }

        return implode("\r\n", $headerLines);
    }

    private function sendSimpleBody(Message $message): void
    {
        if ($message->getHtmlBody() !== '') {
            $boundary = md5(uniqid());
            $this->sendRaw("Content-Type: multipart/alternative; boundary=\"{$boundary}\"");
            $this->sendRaw("");
            $this->sendRaw("--{$boundary}");
            $this->sendRaw("Content-Type: text/plain; charset=UTF-8");
            $this->sendRaw("");
            $this->sendRaw($message->getBody());
            $this->sendRaw("--{$boundary}");
            $this->sendRaw("Content-Type: text/html; charset=UTF-8");
            $this->sendRaw("");
            $this->sendRaw($message->getHtmlBody());
            $this->sendRaw("--{$boundary}--");
        } else {
            $this->sendRaw("Content-Type: text/plain; charset=UTF-8");
            $this->sendRaw("");
            $this->sendRaw($message->getBody());
        }
    }

    private function sendBodyPart(string $boundary, Message $message): void
    {
        $this->sendRaw("--{$boundary}");
        $this->sendRaw("Content-Type: text/plain; charset=UTF-8");
        $this->sendRaw("");
        $this->sendRaw($message->getBody());

        if ($message->getHtmlBody() !== '') {
            $this->sendRaw("--{$boundary}");
            $this->sendRaw("Content-Type: text/html; charset=UTF-8");
            $this->sendRaw("");
            $this->sendRaw($message->getHtmlBody());
        }
    }

    private function sendAttachmentPart(string $boundary, Message $message): void
    {
        foreach ($message->getAttachments() as $attachment) {
            $this->sendRaw("--{$boundary}");
            $this->sendRaw("Content-Type: application/octet-stream; name=\"{$attachment['name']}\"");
            $this->sendRaw("Content-Transfer-Encoding: base64");
            $this->sendRaw("Content-Disposition: attachment; filename=\"{$attachment['name']}\"");
            $this->sendRaw("");
            $this->sendRaw(chunk_split(base64_encode(file_get_contents($attachment['path']))));
        }
    }

    private function extractEmail(string $address): string
    {
        if (preg_match('/<(.+)>/', $address, $matches)) {
            return $matches[1];
        }
        return $address;
    }

    private function startTls(): void
    {
        $crypto = stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
        if (!$crypto) {
            throw new \RuntimeException("STARTTLS failed");
        }
    }
}
