<?php

namespace Liberta\Cli\Commands;

use Liberta\Cli\BaseCommand;

class MakeCommand extends BaseCommand
{
    protected string $basePath;

    public function __construct(string $basePath = '.')
    {
        $this->basePath = $basePath;
    }

    public function name(): string
    {
        return 'make';
    }

    public function description(): string
    {
        return 'Generate code scaffolding (controller, migration, seeder, module)';
    }

    public function usage(): string
    {
        return <<<'HELP'
Usage: liberta make:<type> <name> [options]

Types:
  controller   Generate a controller class
  migration    Generate a migration class
  seeder       Generate a seeder class
  module       Generate a full module directory
  model        Generate a domain model class
  service      Generate a service class
  repository   Generate a repository class

Examples:
  liberta make:controller ProductController
  liberta make:migration create_users_table
  liberta make:seeder UserSeeder
  liberta make:module Inventory

HELP;
    }

    public function execute(array $args): int
    {
        // Determine the type from the command name pattern
        // e.g., "make:controller" → type = "controller"
        $fullCommand = $this->name();
        $type = str_replace('make:', '', $fullCommand);

        if ($type === 'make') {
            // Called as generic "make", check first argument
            $type = $this->arg(0, '');
            $name = $this->arg(1);
        } else {
            $name = $this->arg(0);
        }

        if ($name === null) {
            $this->error("Name is required. Usage: liberta make:{$type} <name>");
            return 1;
        }

        $this->parseArgs($args);

        return match ($type) {
            'controller' => $this->makeController($name),
            'migration'  => $this->makeMigration($name),
            'seeder'     => $this->makeSeeder($name),
            'module'     => $this->makeModule($name),
            'model'      => $this->makeModel($name),
            'service'    => $this->makeService($name),
            'repository' => $this->makeRepository($name),
            default => $this->error("Unknown type: {$type}") ? 1 : 1,
        };
    }

    private function makeController(string $name): int
    {
        $namespace = $this->option('namespace', 'App\\Http\\Controllers');
        $path = $this->option('path', "app/Http/Controllers/{$name}.php");

        $content = <<<PHP
<?php

namespace {$namespace};

use Liberta\Http\Request;
use Liberta\Http\Response;

class {$name}
{
    public function index(Request \$request): Response
    {
        return Response::json([]);
    }

    public function show(Request \$request): Response
    {
        \$id = \$request->params()['id'] ?? null;
        return Response::json(['id' => \$id]);
    }

    public function store(Request \$request): Response
    {
        return Response::json([], 201);
    }

    public function update(Request \$request): Response
    {
        \$id = \$request->params()['id'] ?? null;
        return Response::json(['id' => \$id]);
    }

    public function destroy(Request \$request): Response
    {
        return Response::json(null, 204);
    }
}
PHP;

        return $this->writeFile($path, $content);
    }

    private function makeMigration(string $name): int
    {
        $timestamp = date('Ymd_His');
        $className = $this->studly($name);
        $path = "database/migrations/{$timestamp}_{$name}.php";

        $content = <<<PHP
<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Sql\DB;

class {$className} implements Migration
{
    public function name(): string
    {
        return '{$timestamp}_{$name}';
    }

    public function up(DB \$db): void
    {
        \$schema = new \Liberta\Migration\Schema(\$db);
        \$schema->create('table_name', function (\Liberta\Migration\Table \$table) {
            \$table->id();
            \$table->string('name');
            \$table->timestamps();
        });
    }

    public function down(DB \$db): void
    {
        \$schema = new \Liberta\Migration\Schema(\$db);
        \$schema->drop('table_name');
    }
}
PHP;

        return $this->writeFile($path, $content);
    }

    private function makeSeeder(string $name): int
    {
        $className = $this->studly($name);
        $path = "database/seeders/{$name}.php";

        $content = <<<PHP
<?php

namespace Database\Seeders;

use Liberta\Seeder\Seeder;
use Liberta\Sql\DB;

class {$className} implements Seeder
{
    public function name(): string
    {
        return '{$className}';
    }

    public function run(DB \$db): void
    {
        // TODO: Seed data
    }
}
PHP;

        return $this->writeFile($path, $content);
    }

    private function makeModule(string $name): int
    {
        $lower = strtolower($name);
        $base = "modules/{$lower}";

        $files = [
            "{$base}/src/{$name}Module.php" => $this->moduleStub($name, $lower),
            "{$base}/src/Http/Controllers/{$name}Controller.php" => $this->moduleControllerStub($name),
            "{$base}/src/Services/{$name}Service.php" => $this->moduleServiceStub($name),
            "{$base}/src/Repositories/{$name}Repository.php" => $this->moduleRepositoryStub($name),
            "{$base}/routes/api.php" => "<?php\n\n/** @var \\Liberta\\Router\\Router \$router */\n\n",
        ];

        foreach ($files as $path => $content) {
            $this->writeFile($path, $content);
        }

        $this->success("Module [{$name}] created at {$base}/");
        return 0;
    }

    private function makeModel(string $name): int
    {
        $path = $this->option('path', "app/Domain/{$name}.php");

        $content = <<<PHP
<?php

namespace App\Domain;

class {$name}
{
    public function __construct(
        public readonly string \$id,
        // TODO: add properties
    ) {}

    public function toArray(): array
    {
        return [
            'id' => \$this->id,
            // TODO: add fields
        ];
    }
}
PHP;

        return $this->writeFile($path, $content);
    }

    private function makeService(string $name): int
    {
        $path = $this->option('path', "app/Services/{$name}.php");

        $content = <<<PHP
<?php

namespace App\Services;

class {$name}
{
    public function __construct(
        // TODO: inject dependencies
    ) {}
}
PHP;

        return $this->writeFile($path, $content);
    }

    private function makeRepository(string $name): int
    {
        $path = $this->option('path', "app/Repositories/{$name}.php");

        $content = <<<PHP
<?php

namespace App\Repositories;

use Liberta\Sql\DB;

class {$name}
{
    public function __construct(
        protected DB \$db
    ) {}

    public function findById(string \$id): ?array
    {
        return \$this->db->table('table_name')
            ->where('id', '=', \$id)
            ->first();
    }
}
PHP;

        return $this->writeFile($path, $content);
    }

    private function writeFile(string $path, string $content): int
    {
        $fullPath = $this->basePath . '/' . $path;
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($fullPath) && !$this->option('force')) {
            $this->error("File already exists: {$path}. Use --force to overwrite.");
            return 1;
        }

        file_put_contents($fullPath, $content);
        $this->success("Created: {$path}");
        return 0;
    }

    private function moduleStub(string $name, string $lower): string
    {
        return <<<PHP
<?php

namespace Modules\\{$name};

use Liberta\\Router\\Module;
use Liberta\\Router\\Router;

class {$name}Module implements Module
{
    public function name(): string
    {
        return '{$lower}';
    }

    public function routes(Router \$router): void
    {
        require __DIR__ . '/../routes/api.php';
    }

    public function middleware(): array
    {
        return ['auth'];
    }
}
PHP;
    }

    private function moduleControllerStub(string $name): string
    {
        return <<<PHP
<?php

namespace Modules\\{$name}\\Http\\Controllers;

use Liberta\\Http\\Request;
use Liberta\\Http\\Response;

class {$name}Controller
{
    public function index(Request \$request): Response
    {
        return Response::json([]);
    }
}
PHP;
    }

    private function moduleServiceStub(string $name): string
    {
        return <<<PHP
<?php

namespace Modules\\{$name}\\Services;

class {$name}Service
{
    public function __construct() {}
}
PHP;
    }

    private function moduleRepositoryStub(string $name): string
    {
        return <<<PHP
<?php

namespace Modules\\{$name}\\Repositories;

use Liberta\\Sql\\DB;

class {$name}Repository
{
    public function __construct(
        protected DB \$db
    ) {}
}
PHP;
    }

    private function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }
}
