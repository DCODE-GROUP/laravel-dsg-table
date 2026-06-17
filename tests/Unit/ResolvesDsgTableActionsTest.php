<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Resources\UsersTableResource;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class ResolvesDsgTableActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/admin/users/{user}', fn () => '')->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', fn () => '')->name('admin.users.edit');
        Route::delete('/admin/users/{user}', fn () => '')->name('admin.users.destroy');
    }

    public function test_resource_trait_resolves_actions_from_table_class(): void
    {
        $model = (object) ['id' => 2, 'name' => 'Jane'];

        $payload = (new UsersTableResource($model))->resolve();

        $this->assertSame(2, $payload['id']);
        $this->assertArrayHasKey('actions', $payload);
        $this->assertSame(route('admin.users.show', 2), $payload['actions']['view']['link']);
    }
}
