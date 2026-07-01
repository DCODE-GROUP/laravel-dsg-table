<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Feature;

use Dcodegroup\LaravelDsgTable\Facades\DsgTable;
use Dcodegroup\LaravelDsgTable\Http\Controllers\TableFiltersController;
use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables\UsersTable;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class TableFiltersControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        UsersTable::$authorisedWith = null;

        Route::dsgTable(middleware: []);
    }

    public function test_it_returns_filter_definitions_for_a_registered_route(): void
    {
        $response = $this->getJson('/dsg-table/users/filters');

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.type', 'refines')
            ->assertJsonPath('1.type', 'date_range');
    }

    public function test_it_authorises_before_returning_filters(): void
    {
        $this->getJson('/dsg-table/users/filters/99')->assertOk();

        $this->assertSame('99', UsersTable::$authorisedWith);
    }

    public function test_it_returns_not_found_when_table_class_is_missing(): void
    {
        $this->getJson('/dsg-table/missing/filters')->assertNotFound();
    }

    public function test_controller_can_be_invoked_directly(): void
    {
        $response = app(TableFiltersController::class)(request(), 'users');

        $this->assertCount(3, json_decode($response->getContent(), true));
    }

    public function test_facade_returns_filter_definitions(): void
    {
        $this->assertCount(3, DsgTable::filters('users'));
    }
}
