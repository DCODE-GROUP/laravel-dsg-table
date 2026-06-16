<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Feature;

use Dcodegroup\LaravelDsgTable\Http\Controllers\TableController;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class RouteMacroTest extends TestCase
{
    public function test_dsg_table_route_macro_uses_configured_defaults(): void
    {
        config([
            'dsg-table.route.prefix' => 'configured-prefix',
            'dsg-table.route.name' => 'configured.route',
            'dsg-table.route.middleware' => [],
        ]);

        Route::dsgTable();

        $route = collect(Route::getRoutes())->first(
            fn ($route) => $route->getName() === 'configured.route'
        );

        $this->assertNotNull($route);
        $this->assertSame('configured-prefix/{tableName}/{param?}', $route->uri());
        $this->assertStringContainsString(
            TableController::class,
            (string) $route->getAction('uses'),
        );
    }
}
