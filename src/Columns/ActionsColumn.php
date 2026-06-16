<?php

namespace Dcodegroup\LaravelDsgTable\Columns;

class ActionsColumn extends Column
{
    public static function make(?string $title = null, ?string $width = '100px', ?string $dataClass = null): static
    {
        $column = parent::make('actions', $title ?? '', $dataClass ?? static::defaultDataClass('actions'));

        return $column->width($width);
    }
}
