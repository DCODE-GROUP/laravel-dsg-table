<?php

namespace Dcodegroup\LaravelDsgTable\Facades;

use Dcodegroup\LaravelDsgTable\Support\AbstractTableFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dcodegroup\LaravelDsgTable\Contracts\TableInterface get(string $tableName)
 * @method static array<int, array<string, mixed>> fields(string $tableName)
 * @method static array<int, array<string, mixed>> filters(string $tableName, ?\Illuminate\Http\Request $request = null, mixed $param = null)
 * @method static array<string, array<string, mixed>> actionsFor(string $tableName, mixed $model, mixed $param = null)
 *
 * @see AbstractTableFactory
 */
class DsgTable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AbstractTableFactory::class;
    }
}
