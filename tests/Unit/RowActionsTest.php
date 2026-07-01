<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Actions\CrudActions;
use Dcodegroup\LaravelDsgTable\Actions\RowActions;
use Dcodegroup\LaravelDsgTable\Actions\TableAction;
use Dcodegroup\LaravelDsgTable\Support\TableFactory;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class RowActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/admin/users/{user}', fn () => '')->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', fn () => '')->name('admin.users.edit');
        Route::delete('/admin/users/{user}', fn () => '')->name('admin.users.destroy');
        Route::get('/admin/providers/{provider}/contracts/{contract}/edit', fn () => '')->name('admin.providers.contracts.edit');
        Route::delete('/admin/providers/{provider}/contracts/{contract}', fn () => '')->name('admin.providers.contracts.destroy');
    }

    public function test_crud_actions_builds_view_edit_delete_actions(): void
    {
        $model = (object) ['id' => 5];

        $actions = CrudActions::for($model, 'admin.users')
            ->withView()
            ->withEdit()
            ->withDelete('Confirm delete?')
            ->toArray();

        $this->assertSame(route('admin.users.show', 5), $actions['view']['link']);
        $this->assertSame(__('dsg-table::actions.view'), $actions['view']['label']);
        $this->assertSame(route('admin.users.edit', 5), $actions['edit']['link']);
        $this->assertSame(route('admin.users.destroy', 5), $actions['delete']['link']);
        $this->assertSame('DeleteConfirmationButton', $actions['delete']['component']);
        $this->assertSame('Confirm delete?', $actions['delete']['content']);
    }

    public function test_row_actions_supports_nested_route_parameters(): void
    {
        $model = (object) ['id' => 9, 'provider_id' => 3];

        $actions = RowActions::for($model)
            ->edit('admin.providers.contracts.edit', [
                'provider' => $model->provider_id,
                'contract' => $model,
            ])
            ->delete(
                'admin.providers.contracts.destroy',
                [
                    'provider' => $model->provider_id,
                    'contract' => $model,
                ],
                content: 'Delete contract?',
            )
            ->toArray();

        $this->assertSame(
            route('admin.providers.contracts.edit', ['provider' => 3, 'contract' => 9]),
            $actions['edit']['link'],
        );
        $this->assertSame('Delete contract?', $actions['delete']['content']);
    }

    public function test_row_actions_can_be_conditionally_added(): void
    {
        $model = (object) ['id' => 1];

        $actions = RowActions::for($model)
            ->when(false, fn (RowActions $builder) => $builder->view('admin.users.show'))
            ->when(true, fn (RowActions $builder) => $builder->edit('admin.users.edit'))
            ->toArray();

        $this->assertArrayNotHasKey('view', $actions);
        $this->assertArrayHasKey('edit', $actions);
    }

    public function test_table_action_delete_omits_empty_component_and_content(): void
    {
        config(['dsg-table.actions.delete.component' => null]);

        $action = TableAction::delete('admin.users.destroy', ['user' => 1]);

        $this->assertArrayNotHasKey('component', $action);
        $this->assertArrayNotHasKey('content', $action);
    }

    public function test_users_table_resolves_actions_for_a_model(): void
    {
        $model = (object) ['id' => 7];

        $actions = app(TableFactory::class)
            ->actionsFor('users', $model);

        $this->assertArrayHasKey('view', $actions);
        $this->assertArrayHasKey('edit', $actions);
        $this->assertArrayHasKey('delete', $actions);
        $this->assertSame(route('admin.users.show', 7), $actions['view']['link']);
    }
}
