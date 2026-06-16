<?php

namespace Dcodegroup\LaravelDsgTable\Facets;

use Dcodegroup\LaravelDsgTable\Contracts\Facetable;
use Dcodegroup\LaravelDsgTable\Support\RefineItems;
use Illuminate\Database\Eloquent\Builder;

class RefineFacet implements Facetable
{
    /**
     * @param  iterable<int, mixed>|Builder  $items
     */
    public function __construct(
        protected string $name,
        protected string $key,
        protected iterable|Builder $items,
        protected string $searchField = 'name',
        protected string $valueField = 'id',
        protected bool $apiMode = true,
    ) {}

    /**
     * @param  iterable<int, mixed>|Builder  $items
     */
    public static function make(
        string $name,
        string $key,
        iterable|Builder $items,
        string $searchField = 'name',
        string $valueField = 'id',
        bool $apiMode = true,
    ): static {
        return new static($name, $key, $items, $searchField, $valueField, $apiMode);
    }

    public function toFilterDefinition(): array
    {
        $items = $this->items instanceof Builder ? $this->items->get() : $this->items;

        return [
            'key' => $this->key,
            'name' => $this->name,
            'api_mode' => $this->apiMode,
            'type' => 'refines',
            'refines' => RefineItems::from($items, $this->searchField, $this->valueField),
        ];
    }
}
