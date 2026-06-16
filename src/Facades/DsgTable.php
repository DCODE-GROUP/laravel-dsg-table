<?php

namespace Dcodegroup\LaravelDsgTable\Facades;

use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Dcodegroup\LaravelDsgTable\Support\AbstractTableFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dcodegroup\LaravelDsgTable\Contracts\TableInterface get(string $tableName)
 * @method static array<int, array<string, mixed>> fields(string $tableName)
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
