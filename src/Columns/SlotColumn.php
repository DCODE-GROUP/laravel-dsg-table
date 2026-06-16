<?php

namespace Dcodegroup\LaravelDsgTable\Columns;

use Illuminate\Support\Str;

/**
 * A column rendered via a named slot on the DsgTable Vue component.
 *
 * Slot name convention: dsg-field--{field}
 */
class SlotColumn extends Column
{
    public static function make(string $field, ?string $title = null, ?string $dataClass = null): static
    {
        $instance = new static;

        $instance->column = [
            'name' => $field,
            'title' => $title ?? Str::headline($field),
            'field' => $field,
        ];

        $instance->applyDataClass($dataClass ?? static::defaultDataClass('slot'));

        return $instance;
    }
}
