<?php

namespace Dcodegroup\LaravelDsgTable\Support;

class TableFactory extends AbstractTableFactory
{
    protected function tablesNamespace(): string
    {
        return config('dsg-table.tables_namespace');
    }

    protected function classSuffix(): string
    {
        return config('dsg-table.class_suffix', 'Table');
    }
}
