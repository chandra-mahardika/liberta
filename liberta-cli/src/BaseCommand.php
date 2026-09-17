<?php

namespace Liberta\Cli;

abstract class BaseCommand implements Command
{
    protected array $arguments = [];
    protected array $options = [];

    public function usage(): string
    {
        return "liberta {$this->name()}";
    }

    /**
     * Output text to console.
     */
    protected function info(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    /**
     * Output error to stderr.
     */
    protected function error(string $message): void
    {
        fwrite(STDERR, "ERROR: {$message}" . PHP_EOL);
    }

    /**
     * Output success message.
     */
    protected function success(string $message): void
    {
        fwrite(STDOUT, "OK: {$message}" . PHP_EOL);
    }

    /**
     * Ask for input.
     */
    protected function ask(string $prompt): string
    {
        fwrite(STDOUT, "{$prompt}: ");
        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        fclose($handle);

        return trim($line);
    }

    /**
     * Confirm a prompt (y/n).
     */
    protected function confirm(string $prompt): bool
    {
        $answer = $this->ask("{$prompt} (yes/no)");
        return in_array(strtolower($answer), ['yes', 'y'], true);
    }

    /**
     * Parse arguments from $args array.
     */
    protected function parseArgs(array $args): void
    {
        $this->arguments = [];
        $this->options = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $parts = explode('=', substr($arg, 2), 2);
                $key = $parts[0];
                $value = $parts[1] ?? true;
                $this->options[$key] = $value;
            } elseif (str_starts_with($arg, '-')) {
                $key = substr($arg, 1);
                $this->options[$key] = true;
            } else {
                $this->arguments[] = $arg;
            }
        }
    }

    /**
     * Get a positional argument by index.
     */
    protected function arg(int $index, ?string $default = null): ?string
    {
        return $this->arguments[$index] ?? $default;
    }

    /**
     * Get an option value.
     */
    protected function option(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }
}
