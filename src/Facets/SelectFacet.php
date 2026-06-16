<?php

namespace Dcodegroup\LaravelDsgTable\Facets;

use Dcodegroup\LaravelDsgTable\Contracts\Facetable;
use Dcodegroup\LaravelDsgTable\Support\RefineItems;

class SelectFacet implements Facetable
{
    /**
     * @param  iterable<int, mixed>  $items
     */
    public function __construct(
        protected string $name,
        protected string $key,
        protected iterable $items,
        protected string $searchField = 'label',
        protected string $valueField = 'value',
        protected bool $apiMode = false,
    ) {}

    /**
     * @param  iterable<int, mixed>  $items
     */
    public static function make(
        string $name,
        string $key,
        iterable $items,
        string $searchField = 'label',
        string $valueField = 'value',
        bool $apiMode = false,
    ): static {
        return new static($name, $key, $items, $searchField, $valueField, $apiMode);
    }

    public function toFilterDefinition(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'api_mode' => $this->apiMode,
            'type' => 'refines',
            'refines' => RefineItems::from(
                $this->items,
                $this->searchField,
                $this->valueField,
            ),
        ];
    }
}
