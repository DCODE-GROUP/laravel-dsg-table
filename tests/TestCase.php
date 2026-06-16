<?php

namespace Dcodegroup\LaravelDsgTable\Tests;

use Dcodegroup\LaravelDsgTable\DsgTableServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DsgTableServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'DsgTable' => \Dcodegroup\LaravelDsgTable\Facades\DsgTable::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('dsg-table.tables_namespace', 'Dcodegroup\\LaravelDsgTable\\Tests\\Fixtures\\Tables');
        $app['config']->set('dsg-table.tables_path', __DIR__.'/Fixtures/Tables');
        $app['config']->set('dsg-table.class_suffix', 'Table');
        $app['config']->set('dsg-table.route.auto_register', false);
    }

    protected function tempTablesPath(): string
    {
        $path = sys_get_temp_dir().'/laravel-dsg-table-'.uniqid('', true);

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }
}
