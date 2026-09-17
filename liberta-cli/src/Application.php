<?php

namespace Liberta\Cli;

class Application
{
    /** @var array<string, Command> */
    private array $commands = [];

    public function __construct(
        protected string $version = '1.0.0'
    ) {}

    public function register(Command $command): void
    {
        $this->commands[$command->name()] = $command;
    }

    /**
     * @param Command[] $commands
     */
    public function registerMany(array $commands): void
    {
        foreach ($commands as $command) {
            $this->register($command);
        }
    }

    /**
     * Run the CLI application.
     *
     * @param string[] $argv  Raw $argv from PHP
     */
    public function run(array $argv): int
    {
        // $argv[0] = script, $argv[1] = command name, $argv[2..] = args
        $commandName = $argv[1] ?? null;

        if ($commandName === null || $commandName === 'help') {
            $this->showHelp();
            return 0;
        }

        if (!isset($this->commands[$commandName])) {
            fwrite(STDERR, "Unknown command: {$commandName}" . PHP_EOL);
            $this->showHelp();
            return 1;
        }

        $command = $this->commands[$commandName];
        $args = array_slice($argv, 2);

        try {
            return $command->execute($args);
        } catch (\Throwable $e) {
            fwrite(STDERR, "Error: {$e->getMessage()}" . PHP_EOL);
            return 1;
        }
    }

    private function showHelp(): void
    {
        $this->info("Liberta CLI v{$this->version}");
        $this->info("");
        $this->info("Usage: liberta <command> [options]");
        $this->info("");
        $this->info("Available commands:");

        foreach ($this->commands as $name => $command) {
            $this->info(sprintf("  %-25s %s", $name, $command->description()));
        }

        $this->info("");
        $this->info("Run 'liberta <command> --help' for more info on a command.");
    }

    private function info(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }
}
