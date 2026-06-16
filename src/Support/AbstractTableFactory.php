<?php

namespace Dcodegroup\LaravelDsgTable\Support;

use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Dcodegroup\LaravelDsgTable\Exceptions\TableException;
use Dcodegroup\LaravelDsgTable\Exceptions\TableNotFoundException;
use Illuminate\Support\Str;

abstract class AbstractTableFactory
{
    public function get(string $tableName): TableInterface
    {
        $class = $this->resolveClassName($tableName);

        if (! class_exists($class)) {
            throw TableNotFoundException::make($class, $tableName);
        }

        $table = app($class);

        if (! $table instanceof TableInterface) {
            throw TableException::interfaceNotImplemented($class);
        }

        return $table;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fields(string $tableName): array
    {
        return $this->get($tableName)->fields()->values()->all();
    }

    protected function resolveClassName(string $tableName): string
    {
        return $this->tablesNamespace()
            .'\\'
            .Str::studly($tableName)
            .$this->classSuffix();
    }

    abstract protected function tablesNamespace(): string;

    abstract protected function classSuffix(): string;
}
