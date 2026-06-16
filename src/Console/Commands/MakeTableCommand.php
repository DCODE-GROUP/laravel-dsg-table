<?php

namespace Dcodegroup\LaravelDsgTable\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeTableCommand extends Command
{
    protected $signature = 'make:table {name : The table name (e.g. users or account-users)}';

    protected $description = 'Create a new DSG table class';

    public function handle(Filesystem $files): int
    {
        $name = $this->argument('name');
        $suffix = config('dsg-table.class_suffix', 'Table');
        $className = Str::studly($name).$suffix;
        $path = config('dsg-table.tables_path')."/{$className}.php";

        if ($files->exists($path)) {
            $this->components->error("Table class [{$className}] already exists.");

            return self::FAILURE;
        }

        $namespace = config('dsg-table.tables_namespace');
        $stubPath = $files->exists($customStub = base_path('stubs/dsg-table.stub'))
            ? $customStub
            : __DIR__.'/../../../stubs/table.stub';

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $files->get($stubPath),
        );

        $files->ensureDirectoryExists(dirname($path));
        $files->put($path, $stub);

        $this->components->info("Table [{$className}] created successfully.");

        return self::SUCCESS;
    }
}
