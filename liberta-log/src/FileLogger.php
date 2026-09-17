<?php

namespace Liberta\Log;

class FileLogger implements Logger
{
    private string $path;
    private string $minLevel;
    private ?\DateTimeImmutable $date = null;

    private const LEVELS = [
        'emergency' => 0,
        'alert'     => 1,
        'critical'  => 2,
        'error'     => 3,
        'warning'   => 4,
        'notice'    => 5,
        'info'      => 6,
        'debug'     => 7,
    ];

    public function __construct(string $path, string $minLevel = 'debug')
    {
        $this->path = rtrim($path, '/');
        $this->minLevel = $minLevel;

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        if (!isset(self::LEVELS[$level])) {
            throw new \InvalidArgumentException("Unknown log level: {$level}");
        }

        if (self::LEVELS[$level] > self::LEVELS[$this->minLevel]) {
            return;
        }

        $this->date = $this->date ?? new \DateTimeImmutable();
        $formatted = $this->format($level, $message, $context);

        $file = $this->path . '/' . $this->date->format('Y-m-d') . '.log';
        file_put_contents($file, $formatted . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function format(string $level, string $message, array $context): string
    {
        $time = $this->date->format('H:i:s');
        $upper = strtoupper($level);

        if (!empty($context)) {
            $json = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $message .= ' ' . $json;
        }

        return "[{$time}] [{$upper}] {$message}";
    }
}
