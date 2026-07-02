<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Feature;

use Dcodegroup\LaravelDsgTable\Http\Controllers\TableController;
use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables\UsersTable;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class TableControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        UsersTable::$authorisedWith = null;
        UsersTable::$collectionParam = null;

        Route::dsgTable(middleware: []);

        Route::get('/admin/users/{user}', fn () => '')->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', fn () => '')->name('admin.users.edit');
        Route::delete('/admin/users/{user}', fn () => '')->name('admin.users.destroy');
    }

    public function test_it_returns_table_data_for_a_registered_route(): void
    {
        $response = $this->getJson('/dsg-table/users');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Jane Doe')
            ->assertJsonPath('data.1.name', 'John Smith')
            ->assertJsonPath('data.0.actions.view.link', route('admin.users.show', 1));
    }

    public function test_it_passes_optional_route_param_to_the_table(): void
    {
        $this->getJson('/dsg-table/account-users/42')->assertOk()
            ->assertJsonPath('data.0.id', '42')
            ->assertJsonPath('data.0.account_id', '42');
    }

    public function test_it_authorises_before_returning_data(): void
    {
        $this->getJson('/dsg-table/users/99')->assertOk();

        $this->assertSame('99', UsersTable::$authorisedWith);
        $this->assertSame('99', UsersTable::$collectionParam);
    }

    public function test_it_returns_not_found_when_table_class_is_missing(): void
    {
        $this->getJson('/dsg-table/missing')->assertNotFound();
    }

    public function test_controller_can_be_invoked_directly(): void
    {
        $response = app(TableController::class)(
            request(),
            'users',
        );

        $this->assertCount(2, $response->resolve());
    }

    public function test_route_can_be_registered_without_optional_param(): void
    {
        Route::dsgTable(prefix: 'tables-only', name: 'tables-only', middleware: [], withParam: false);

        $this->getJson('/tables-only/users')->assertOk();
    }
}
