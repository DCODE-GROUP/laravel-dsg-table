<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Exceptions\TableException;
use Dcodegroup\LaravelDsgTable\Exceptions\TableNotFoundException;
use Dcodegroup\LaravelDsgTable\Facades\DsgTable;
use Dcodegroup\LaravelDsgTable\Support\TableFactory;
use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables\UsersTable;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;

class TableFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        UsersTable::$authorisedWith = null;
        UsersTable::$collectionParam = null;
    }

    public function test_it_resolves_a_table_class_from_a_kebab_case_name(): void
    {
        $table = app(TableFactory::class)->get('users');

        $this->assertInstanceOf(UsersTable::class, $table);
    }

    public function test_it_resolves_multi_word_table_names(): void
    {
        $table = app(TableFactory::class)->get('account-users');

        $this->assertSame(
            'Dcodegroup\\LaravelDsgTable\\Tests\\Fixtures\\Tables\\AccountUsersTable',
            $table::class,
        );
    }

    public function test_it_uses_configured_namespace_and_suffix(): void
    {
        config([
            'dsg-table.tables_namespace' => 'Dcodegroup\\LaravelDsgTable\\Tests\\Fixtures\\Tables',
            'dsg-table.class_suffix' => 'Table',
        ]);

        $table = app(TableFactory::class)->get('users');

        $this->assertInstanceOf(UsersTable::class, $table);
    }

    public function test_it_throws_when_table_class_does_not_exist(): void
    {
        $this->expectException(TableNotFoundException::class);
        $this->expectExceptionMessage('Table class `Dcodegroup\\LaravelDsgTable\\Tests\\Fixtures\\Tables\\MissingTable` was not found');

        app(TableFactory::class)->get('missing');
    }

    public function test_it_throws_when_table_class_does_not_implement_interface(): void
    {
        $this->expectException(TableException::class);
        $this->expectExceptionMessage('does not implement');

        app(TableFactory::class)->get('broken');
    }

    public function test_fields_returns_a_reindexed_array_of_column_definitions(): void
    {
        $fields = app(TableFactory::class)->fields('users');

        $this->assertSame([
            [
                'name' => 'name',
                'title' => 'Name',
                'dataClass' => 'px-3 py-4 text-sm text-gray-500 wrap-break-word',
            ],
            [
                'name' => 'email',
                'title' => 'Email',
                'dataClass' => 'px-3 py-4 text-sm text-gray-500 wrap-break-word',
                'sortField' => 'email',
            ],
        ], $fields);
    }

    public function test_facade_resolves_tables_and_fields(): void
    {
        $this->assertInstanceOf(UsersTable::class, DsgTable::get('users'));
        $this->assertCount(2, DsgTable::fields('users'));
        $this->assertCount(3, DsgTable::filters('users'));
    }
}
