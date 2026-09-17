<?php

namespace Liberta\Seeder;

use Liberta\Sql\DB;
use Liberta\Log\Logger;
use Liberta\Log\NullLogger;

class SeederRunner
{
    /** @var Seeder[] Indexed by name */
    private array $seeders = [];

    public function __construct(
        protected DB $db,
        protected ?Logger $logger = null
    ) {
        $this->logger ??= new NullLogger();
    }

    public function register(Seeder $seeder): void
    {
        $this->seeders[$seeder->name()] = $seeder;
    }

    /**
     * @param Seeder[] $seeders
     */
    public function registerMany(array $seeders): void
    {
        foreach ($seeders as $seeder) {
            $this->register($seeder);
        }
    }

    /**
     * Run all registered seeders, or specific ones by name.
     *
     * @param string[] $names  Optional: only run these seeders
     */
    public function run(array $names = []): array
    {
        $run = [];

        foreach ($this->seeders as $name => $seeder) {
            if (!empty($names) && !in_array($name, $names, true)) {
                continue;
            }

            $this->logger->info("Running seeder: {$name}");
            $seeder->run($this->db);
            $run[] = $name;
        }

        return $run;
    }

    /**
     * Get list of registered seeder names.
     *
     * @return string[]
     */
    public function getRegistered(): array
    {
        return array_keys($this->seeders);
    }
}
