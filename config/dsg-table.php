<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Classes Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace where your table classes live. The factory resolves a
    | table name such as "users" to {namespace}\Users{suffix}.
    |
    */

    'tables_namespace' => 'App\\Tables',

    /*
    |--------------------------------------------------------------------------
    | Table Classes Path
    |--------------------------------------------------------------------------
    |
    | The directory where the make:table command will scaffold new table
    | classes. This is typically the PSR-4 path for tables_namespace.
    |
    */

    'tables_path' => app_path('Tables'),

    /*
    |--------------------------------------------------------------------------
    | Table Class Suffix
    |--------------------------------------------------------------------------
    |
    | Appended to the studly-cased table name when resolving a class. For
    | example, with suffix "Table", "users" resolves to UsersTable.
    |
    */

    'class_suffix' => 'Table',

    /*
    |--------------------------------------------------------------------------
    | Column Defaults
    |--------------------------------------------------------------------------
    |
    | Default CSS classes applied to table cells. Override globally here, per
    | column type (default, slot, actions), or per column via ->dataClass().
    |
    | Set a value to null to omit dataClass from the column definition.
    |
    */

    'columns' => [
        'data_class' => [
            'default' => 'px-3 py-4 text-sm text-gray-500 wrap-break-word',
            'slot' => 'px-3 py-4 text-sm text-gray-500 wrap-break-word',
            'actions' => 'actions',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | Configure the single data-table endpoint. Register it by calling
    | Route::dsgTable() in your routes file, or set auto_register to true.
    |
    */

    'route' => [
        'auto_register' => false,
        'prefix' => 'dsg-table',
        'name' => 'dsg-table',
        'middleware' => ['api'],
        'param' => true,
    ],

];
