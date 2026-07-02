<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Feature;

use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class AutoRegisterRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/admin/users/{user}', fn () => '')->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', fn () => '')->name('admin.users.edit');
        Route::delete('/admin/users/{user}', fn () => '')->name('admin.users.destroy');
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('dsg-table.route.auto_register', true);
        $app['config']->set('dsg-table.route.prefix', 'auto-dsg-table');
        $app['config']->set('dsg-table.route.name', 'auto.dsg-table');
        $app['config']->set('dsg-table.route.middleware', []);
    }

    public function test_it_registers_the_route_when_auto_register_is_enabled(): void
    {
        $this->getJson('/auto-dsg-table/users')->assertOk();
    }
}
