# Laravel DSG Table

Convention-based data tables for Laravel, designed to work with [`dsg-vue tables`](https://github.com/DCODE-GROUP/dsg-vue).

Instead of creating a dedicated API controller for every table, this package gives you:

- **One endpoint** that resolves table classes by name
- **Table classes** that define authorisation, query logic, and column definitions
- **Column builders** that output the field arrays expected by `DsgTable`
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

This registers a single GET endpoint:

```
GET /dsg-table/{tableName}/{param?}
```

For example, `GET /dsg-table/users` resolves to `App\Tables\UsersTable`.

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
use Dcodegroup\LaravelDsgTable\Columns\ActionsColumn;
use Dcodegroup\LaravelDsgTable\Columns\Column;
use Dcodegroup\LaravelDsgTable\Columns\SlotColumn;
use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Illuminate\Auth\Access\Response;
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
}
```

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
$data = $table->resourceCollection();
```

## Table interface

Every table class must implement `TableInterface`:

| Method | Purpose |
|---|---|
| `authorisation($arguments)` | Authorise the current user before returning data. Receives the optional `{param}` route segment. |
| `resourceCollection($param)` | Build and return the paginated `AnonymousResourceCollection` for the table rows. |
| `fields()` | Return a `Collection` of column definition arrays for the table header. |

When a request hits the endpoint, the controller runs:

```php
$table = DsgTable::get($tableName);
$table->authorisation($param);
return $table->resourceCollection($param);
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

This resolves to `AccountUsersTable` and passes `42` as `$param` to both `authorisation()` and `resourceCollection()`. Useful for scoped tables such as users belonging to a specific account.

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
