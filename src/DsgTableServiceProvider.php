<?php

namespace Dcodegroup\LaravelDsgTable;

use Dcodegroup\LaravelDsgTable\Console\Commands\MakeTableCommand;
use Dcodegroup\LaravelDsgTable\Http\Controllers\TableController;
use Dcodegroup\LaravelDsgTable\Http\Controllers\TableFiltersController;
use Dcodegroup\LaravelDsgTable\Support\AbstractTableFactory;
use Dcodegroup\LaravelDsgTable\Support\TableFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DsgTableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dsg-table.php', 'dsg-table');

        $this->app->singleton(AbstractTableFactory::class, TableFactory::class);
    }

    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureTranslations();
        $this->configureCommands();
        $this->configureRouting();
    }

    protected function configureTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'dsg-table');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../lang' => $this->app->langPath('vendor/dsg-table'),
            ], 'dsg-table-translations');
        }
    }

    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dsg-table.php' => $this->app->configPath('dsg-table.php'),
            ], 'dsg-table-config');

            $this->publishes([
                __DIR__.'/../stubs/table.stub' => $this->app->basePath('stubs/dsg-table.stub'),
            ], 'dsg-table-stubs');
        }
    }

    protected function configureCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeTableCommand::class,
            ]);
        }
    }

    protected function configureRouting(): void
    {
        Route::macro('dsgTable', function (
            ?string $prefix = null,
            ?string $name = null,
            array|string|null $middleware = null,
            bool $withParam = true,
        ) {
            $prefix ??= config('dsg-table.route.prefix', 'dsg-table');
            $name ??= config('dsg-table.route.name', 'dsg-table');
            $middleware ??= config('dsg-table.route.middleware', ['api']);

            $uri = $withParam
                ? "{$prefix}/{tableName}/{param?}"
                : "{$prefix}/{tableName}";

            $filtersUri = $withParam
                ? "{$prefix}/{tableName}/filters/{param?}"
                : "{$prefix}/{tableName}/filters";

            Route::get($filtersUri, TableFiltersController::class)
                ->middleware($middleware)
                ->name("{$name}.filters");

            Route::get($uri, TableController::class)
                ->middleware($middleware)
                ->name($name);
        });

        if (config('dsg-table.route.auto_register')) {
            Route::dsgTable(
                withParam: config('dsg-table.route.param', true),
            );
        }
    }
}
