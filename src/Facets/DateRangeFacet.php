<?php

namespace Dcodegroup\LaravelDsgTable\Facets;

use Dcodegroup\LaravelDsgTable\Contracts\Facetable;

class DateRangeFacet implements Facetable
{
    public function __construct(
        protected string $name,
        protected string $key,
    ) {}

    public static function make(string $name, string $key): static
    {
        return new static($name, $key);
    }

    public function toFilterDefinition(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'type' => 'date_range',
            'value' => null,
        ];
    }
}
