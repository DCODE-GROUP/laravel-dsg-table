<?php

namespace Dcodegroup\LaravelDsgTable\Filters;

use Dcodegroup\LaravelDsgTable\Support\RefineItems;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class FilterBuilder implements Arrayable
{
    /** @var array<int, array<string, mixed>> */
    protected array $filters = [];

    public static function make(): static
    {
        return new static;
    }

    public function date(string $key, string $label, mixed $default = ''): static
    {
        $this->filters[] = [
            'key' => $key,
            'name' => $label,
            'type' => 'date',
            'default' => $default,
        ];

        return $this;
    }

    public function dateRange(string $key, string $label, mixed $default = ''): static
    {
        $this->filters[] = [
            'key' => $key,
            'name' => $label,
            'type' => 'date_range',
            'default' => $default,
        ];

        return $this;
    }

    public function refine(
        string $key,
        string $label,
        Builder $builder,
        string $searchField = 'name',
        string $valueField = 'id',
        bool $apiMode = true,
    ): static {
        $this->refineItems($key, $label, $builder->get(), $searchField, $valueField, $apiMode);

        return $this;
    }

    /**
     * @param  iterable<int, mixed>  $items
     */
    public function refineItems(
        string $key,
        string $label,
        iterable $items,
        string $searchField = 'name',
        string $valueField = 'id',
        bool $apiMode = true,
        bool $itemSelected = false,
    ): static {
        $this->filters[] = [
            'key' => $key,
            'name' => $label,
            'api_mode' => $apiMode,
            'type' => 'refines',
            'refines' => RefineItems::from($items, $searchField, $valueField, $itemSelected),
        ];

        return $this;
    }

    public function singleSelect(
        string $key,
        string $label,
        iterable $items,
        string $searchField = 'name',
        string $valueField = 'id',
        bool $apiMode = true,
    ): static {
        $this->filters[] = [
            'key' => $key,
            'name' => $label,
            'api_mode' => $apiMode,
            'type' => 'refines_single',
            'refines' => RefineItems::from($items, $searchField, $valueField),
        ];

        return $this;
    }

    /**
     * @param  array<int, array<string, mixed>>  $definitions
     */
    public function merge(array $definitions): static
    {
        $this->filters = array_merge($this->filters, $definitions);

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->filters;
    }
}
