# Laravel DSG Table

Convention-based data tables for Laravel, designed to work with [`dsg-vue tables`](https://github.com/DCODE-GROUP/dsg-vue).

Instead of creating a dedicated API controller for every table, this package gives you:

- **One endpoint** that resolves table classes by name
- **Table classes** that define authorisation, query logic, column definitions, and filters
- **Column builders** that output the field arrays expected by `DsgTable`
- **Reusable filter facets** (`BooleanFacet`, `DateRangeFacet`, `SelectFacet`, `RefineFacet`)
- **Row action builders** (`CrudActions`, `RowActions`) configured on table classes
- **Fluent `FilterBuilder`** for composing filter definitions
- **`make:table`** to scaffold new tables quickly

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12, or 13

## Installation

```bash
composer require dcodegroup/laravel-dsg-table
```

Publish the config file:

```bash
php artisan vendor:publish --tag=dsg-table-config
```

## Quick start

### 1. Register the route

In your API routes file:

```php
use Illuminate\Support\Facades\Route;

Route::dsgTable(
    name: 'api.dsg-table',
    middleware: ['api', 'auth:sanctum'],
);
```

This registers two GET endpoints:

```
GET /dsg-table/{tableName}/{param?}
GET /dsg-table/{tableName}/filters/{param?}
```

For example, `GET /dsg-table/users` resolves to `App\Tables\UsersTable`, and `GET /dsg-table/users/filters` returns that table's filter definitions.

Alternatively, set `route.auto_register` to `true` in config and the package will register the route for you using the values in `config/dsg-table.php`.

### 2. Create a table class

```bash
php artisan make:table users
```

This creates `app/Tables/UsersTable.php`:

```php
<?php

namespace App\Tables;

use App\Models\User;
use Dcodegroup\LaravelDsgTable\Actions\CrudActions;
use Dcodegroup\LaravelDsgTable\Columns\ActionsColumn;
use Dcodegroup\LaravelDsgTable\Columns\Column;
use Dcodegroup\LaravelDsgTable\Columns\SlotColumn;
use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Dcodegroup\LaravelDsgTable\Facets\DateRangeFacet;
use Dcodegroup\LaravelDsgTable\Facets\Facet;
use Dcodegroup\LaravelDsgTable\Filters\FilterBuilder;
use Dcodegroup\LaravelDsgTable\Support\ActiveFilter;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UsersTable implements TableInterface
{
    public function authorisation(mixed $arguments = null): Response|bool
    {
        return Gate::authorize('viewAny', User::class);
    }

    public function resourceCollection(mixed $param = null): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(User::class)
            ->allowedFilters(['active', 'email'])
            ->allowedSorts(['full_name', 'email', 'updated_at'])
            ->defaultSort('-updated_at');

        return JsonResource::collection(
            $query->paginate(request('per_page', 15))
        );
    }

    public function fields(): Collection
    {
        return collect([
            SlotColumn::make('active', __('user.columns.status'))->toArray(),
            Column::make('full_name', __('user.columns.name'))->isSortable()->toArray(),
            Column::make('email', __('user.columns.email'))->isSortable()->toArray(),
            Column::make('updated_at', __('user.columns.updated_at'))->isSortable()->toArray(),
            ActionsColumn::make()->toArray(),
        ]);
    }

    public function filters(Request $request, mixed $param = null): array
    {
        return array_merge(
            FilterBuilder::make()
                ->refineItems('active', __('generic.words.status'), ActiveFilter::items(), valueField: 'value', apiMode: false)
                ->toArray(),
            Facet::collection([
                DateRangeFacet::make(__('generic.filters.creation_date'), 'created_at'),
            ]),
        );
    }

    public function actionsFor(mixed $model, mixed $param = null): array
    {
        return CrudActions::for($model, 'admin.users', $param)
            ->withView(label: __('generic.buttons.view_details'))
            ->withEdit()
            ->withDelete(__('user.message.confirm_delete'))
            ->toArray();
    }
}
```

Row actions are resolved automatically when data is returned through the table endpoint. Define `actionsFor()` on your table class; `TableController` wraps the resource collection in `DsgTableResourceCollection`, which injects an `actions` key on each row. Your API resource does not need to include actions:

```php
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'full_name' => $this->resource->full_name,
        ];
    }
}
```

If a resource already includes an `actions` key, the collection will not override it.

### 3. Pass fields to your frontend

In your page controller:

```php
use Dcodegroup\LaravelDsgTable\Facades\DsgTable;

return inertia('Admin/Users/Index', [
    'fields' => DsgTable::fields('users'),
]);
```

Or in a Blade view:

```blade
<DsgTable
    :get-url="{{ json_encode(route('api.dsg-table', ['tableName' => 'users'])) }}"
    :fields='@json(DsgTable::fields("users"))'
/>
```

### 4. Render the table in Vue

```vue
<script setup>
import { DsgTable, DsgTablePerPage } from '@dsg/table';
</script>

<template>
  <DsgTable
    ref="dsgTableRef"
    :get-url="route('api.dsg-table', { tableName: 'users' })"
    :filter-endpoint="route('api.dsg-table.filters', { tableName: 'users' })"
    :fields="fields"
    @dsg-table:action-edit="(event, data) => editUser(data)"
    @dsg-table:action-delete="(event, data) => deleteUser(data)"
  >
    <template #dsg-field--active="data">
      <!-- Custom slot for the "active" column -->
    </template>

    <template #dsg-table--footer-left-extras="slotProps">
      <DsgTablePerPage :per-page="slotProps.perPage" />
    </template>
  </DsgTable>
</template>
```

## How table resolution works

Given a table name in the URL, the factory resolves a class using convention over configuration:

| URL segment | Config | Resolved class |
|---|---|---|
| `users` | namespace `App\Tables`, suffix `Table` | `App\Tables\UsersTable` |
| `account-users` | same | `App\Tables\AccountUsersTable` |

Table names use kebab-case in the URL and resolve to StudlyCase class names.

You can resolve a table manually anywhere in your app:

```php
use Dcodegroup\LaravelDsgTable\Facades\DsgTable;

$table = DsgTable::get('users');
$fields = DsgTable::fields('users');
$filters = DsgTable::filters('users');
$actions = DsgTable::actionsFor('users', $user);
$data = $table->resourceCollection();
```

## Table interface

Every table class must implement `TableInterface`:

| Method | Purpose |
|---|---|
| `authorisation($arguments)` | Authorise the current user before returning data or filters. Receives the optional `{param}` route segment. |
| `resourceCollection($param)` | Build and return the paginated `AnonymousResourceCollection` for the table rows. |
| `fields()` | Return a `Collection` of column definition arrays for the table header. |
| `filters($request, $param)` | Return DSG-compatible filter definitions for the frontend. |
| `actionsFor($model, $param)` | Return DSG-compatible row action definitions for a single record. |

When a request hits the data endpoint, the controller runs:

```php
$table = DsgTable::get($tableName);
$table->authorisation($param);
return $table->resourceCollection($param);
```

When a request hits the filters endpoint:

```php
$table = DsgTable::get($tableName);
$table->authorisation($param);
return $table->filters($request, $param);
```

## Configuration

All options live in `config/dsg-table.php`.

### Table class location

```php
'tables_namespace' => 'App\\Tables',
'tables_path' => app_path('Tables'),
'class_suffix' => 'Table',
```

Point these at wherever you want table classes to live in your project. The namespace and path should match your PSR-4 autoloading setup.

### Route defaults

```php
'route' => [
    'auto_register' => false,
    'prefix' => 'dsg-table',
    'name' => 'dsg-table',
    'middleware' => ['api'],
    'param' => true,
],
```

When calling `Route::dsgTable()`, any argument you omit falls back to these config values.

### Column CSS defaults

```php
'columns' => [
    'data_class' => [
        'default' => 'px-3 py-4 text-sm text-gray-500 wrap-break-word',
        'slot' => 'px-3 py-4 text-sm text-gray-500 wrap-break-word',
        'actions' => 'actions',
    ],
],
```

Set any value to `null` to omit `dataClass` from the column output.

### Filter labels

```php
'boolean_facet' => [
    'true_label' => 'dsg-table::filters.yes',
    'false_label' => 'dsg-table::filters.no',
],
'active_filter' => [
    'active_label' => 'dsg-table::filters.active',
    'inactive_label' => 'dsg-table::filters.inactive',
],
'actions' => [
    'labels' => [
        'view' => 'generic.buttons.view',
        'edit' => 'generic.buttons.edit',
        'delete' => 'generic.buttons.delete',
    ],
    'delete' => [
        'component' => 'DeleteConfirmationButton',
    ],
],
```

Override in your app config to use existing translation keys.

Publish translations:

```bash
php artisan vendor:publish --tag=dsg-table-translations
```

## Filters

Each table class defines its own filters via `filters()`. Returns a **JSON array** compatible with DSG Table (`refines`, `date_range`, `refines_single`, etc.).

### Fluent FilterBuilder

```php
FilterBuilder::make()
    ->refineItems('active', __('generic.words.status'), ActiveFilter::items(), valueField: 'value', apiMode: false)
    ->dateRange('created_at', 'Created')
    ->singleSelect('role', 'Role', $roles, searchField: 'label', valueField: 'value')
    ->toArray();
```

### Facets

| Facet | DSG type |
|---|---|
| `BooleanFacet` | `refines` (translated Yes/No by default) |
| `SelectFacet` | `refines` |
| `RefineFacet` | `refines` (from collection or Eloquent builder) |
| `DateRangeFacet` | `date_range` |

```php
Facet::build(BooleanFacet::make('Status', 'active'));
Facet::collection([DateRangeFacet::make('Created', 'created_at')]);
```

### Active / inactive status items

```php
use Dcodegroup\LaravelDsgTable\Support\ActiveFilter;

ActiveFilter::items(); // Collection of [['name' => 'Active', 'value' => 1], ...]
```

## Row actions

Define actions once on the table class via `actionsFor()`. When the table endpoint returns data, `DsgTableResourceCollection` resolves those actions for each row automatically.

### CrudActions shorthand

When routes follow `{prefix}.show`, `{prefix}.edit`, and `{prefix}.destroy`:

```php
public function actionsFor(mixed $model, mixed $param = null): array
{
    return CrudActions::for($model, 'admin.providers', $param)
        ->withView()
        ->withEdit()
        ->withDelete(__('provider.message.confirm_delete'))
        ->toArray();
}
```

### RowActions for custom or nested routes

```php
use Dcodegroup\LaravelDsgTable\Actions\RowActions;
use Illuminate\Support\Facades\Gate;

public function actionsFor(mixed $model, mixed $param = null): array
{
    return RowActions::for($model, $param)
        ->when(Gate::allows('view', $model), fn ($actions) => $actions
            ->view('admin.providers.show'))
        ->when(Gate::allows('update', $model), fn ($actions) => $actions
            ->edit('admin.providers.contracts.edit', [
                'provider' => $model->provider_id,
                'contract' => $model,
            ]))
        ->when(Gate::allows('delete', $model), fn ($actions) => $actions
            ->delete(
                'admin.providers.contracts.destroy',
                ['provider' => $model->provider_id, 'contract' => $model],
                content: __('provider.contract.message.confirm_delete'),
            ))
        ->toArray();
}
```

### Custom actions key

By default, actions are added under the `actions` key. Pass a different key when wrapping manually:

```php
use Dcodegroup\LaravelDsgTable\Http\Resources\DsgTableResourceCollection;

DsgTableResourceCollection::forTable($collection, $table, $param, actionsKey: 'row_actions');
```

## Columns

The package provides three column builders. Each returns an array via `->toArray()` that matches the shape expected by `DsgTable`.

### Column

Standard text/data columns.

```php
Column::make('email')
    ->isSortable()
    ->width('200px')
    ->toArray();

Column::make('email', 'Email address', 'text-sm font-medium')
    ->isSortable(sortField: 'email')
    ->toArray();
```

Available methods:

| Method | Description |
|---|---|
| `isSortable($sortable, $sortField)` | Add a `sortField` key for server-side sorting |
| `dataClass($class)` | Set or replace the cell CSS class. Pass `null` to remove. |
| `appendDataClass($class)` | Append a CSS class to the existing value |
| `withoutDataClass()` | Remove `dataClass` from the output |
| `width($width)` | Set column width |
| `style($style)` | Set inline style |
| `type($type)` | Set column type |
| `overrideDisplay($field)` | Use a different field name for display |

### SlotColumn

Columns rendered via a Vue slot on `DsgTable`. The slot name follows the pattern `dsg-field--{field}`.

```php
SlotColumn::make('active', __('user.columns.status'))->toArray();
```

```vue
<template #dsg-field--active="data">
  <DsgBadge :label="data.rowData.active.value" />
</template>
```

Uses the `slot` key from `columns.data_class` config by default.

### ActionsColumn

The actions column, typically rendered by `DsgTable` with built-in view/edit/delete buttons.

```php
ActionsColumn::make(
    title: '',
    width: '100px',
    dataClass: 'actions',
)->toArray();
```

Uses the `actions` key from `columns.data_class` config by default.

## Optional route parameter

When `route.param` is enabled (the default), the endpoint accepts an optional third segment:

```
GET /dsg-table/account-users/42
```

This resolves to `AccountUsersTable` and passes `42` as `$param` to `authorisation()`, `resourceCollection()`, and `filters()`. Useful for scoped tables such as users belonging to a specific account.

Filters for a scoped table use the same optional parameter:

```
GET /dsg-table/account-users/42/filters
```

Disable it when registering the route:

```php
Route::dsgTable(withParam: false);
```

## Scaffolding

Generate a new table class:

```bash
php artisan make:table users
php artisan make:table account-users
```

The command reads `tables_namespace`, `tables_path`, and `class_suffix` from config.

### Custom stub

Publish the stub to customise the generated class:

```bash
php artisan vendor:publish --tag=dsg-table-stubs
```

This copies the stub to `stubs/dsg-table.stub` in your project root. The `make:table` command will use your published stub when it exists.

## Extending the factory

The default `TableFactory` reads namespace and suffix from config. To customise resolution logic, bind your own factory in a service provider:

```php
use Dcodegroup\LaravelDsgTable\Support\AbstractTableFactory;

$this->app->singleton(AbstractTableFactory::class, function () {
    return new class extends AbstractTableFactory
    {
        protected function tablesNamespace(): string
        {
            return 'App\\Support\\DataTables\\Tables';
        }

        protected function classSuffix(): string
        {
            return 'Vuetable';
        }
    };
});
```

The `DsgTable` facade and `TableController` will use your binding automatically.

## License

MIT. See [LICENSE](LICENSE) for details.
