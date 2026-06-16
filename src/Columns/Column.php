<?php

namespace Dcodegroup\LaravelDsgTable\Columns;

use Dcodegroup\LaravelDsgTable\Columns\Concerns\ConfiguresDataClass;
use Illuminate\Support\Str;

class Column
{
    use ConfiguresDataClass;

    protected array $column = [];

    public static function make(string $name, ?string $title = null, ?string $dataClass = null): static
    {
        $instance = new static;

        $instance->column = [
            'name' => $name,
            'title' => $title ?? Str::headline($name),
        ];

        $instance->applyDataClass($dataClass ?? static::defaultDataClass());

        return $instance;
    }

    protected function applyDataClass(?string $dataClass): void
    {
        if ($dataClass !== null && $dataClass !== '') {
            $this->column['dataClass'] = $dataClass;
        }
    }

    public function isSortable(bool $sortable = true, ?string $sortField = null): static
    {
        if ($sortable) {
            $this->column['sortField'] = $sortField ?? $this->column['name'];
        }

        return $this;
    }

    public function width(?string $width): static
    {
        if ($width) {
            $this->column['width'] = $width;
        }

        return $this;
    }

    public function style(?string $style): static
    {
        if ($style) {
            $this->column['style'] = $style;
        }

        return $this;
    }

    public function type(?string $type): static
    {
        if ($type) {
            $this->column['type'] = $type;
        }

        return $this;
    }

    public function overrideDisplay(string $overrideDisplay): static
    {
        $this->column['formatted'] = $overrideDisplay;
        $this->column['name'] = $overrideDisplay;

        return $this;
    }

    public function toArray(): array
    {
        return $this->column;
    }
}
