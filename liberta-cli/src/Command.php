<?php

namespace Liberta\Cli;

interface Command
{
    /**
     * Command name (e.g., "migrate", "make:controller").
     */
    public function name(): string;

    /**
     * Command description.
     */
    public function description(): string;

    /**
     * Command arguments/usage help.
     */
    public function usage(): string;

    /**
     * Execute the command.
     *
     * @param string[] $args  Command-line arguments (excluding script name and command name)
     */
    public function execute(array $args): int;
}
