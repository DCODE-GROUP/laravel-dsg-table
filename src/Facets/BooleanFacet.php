<?php

namespace Dcodegroup\LaravelDsgTable\Facets;

use Dcodegroup\LaravelDsgTable\Contracts\Facetable;
use Dcodegroup\LaravelDsgTable\Support\RefineItems;

class BooleanFacet implements Facetable
{
    public function __construct(
        protected string $name,
        protected string $key,
        protected bool $apiMode = false,
        protected ?string $trueLabel = null,
        protected ?string $falseLabel = null,
    ) {}

    public static function make(
        string $name,
        string $key,
        bool $apiMode = false,
        ?string $trueLabel = null,
        ?string $falseLabel = null,
    ): static {
        return new static($name, $key, $apiMode, $trueLabel, $falseLabel);
    }

    public function toFilterDefinition(): array
    {
        $trueLabel = $this->trueLabel ?? __(config('dsg-table.boolean_facet.true_label'));
        $falseLabel = $this->falseLabel ?? __(config('dsg-table.boolean_facet.false_label'));

        return [
            'key' => $this->key,
            'name' => $this->name,
            'api_mode' => $this->apiMode,
            'type' => 'refines',
            'refines' => RefineItems::from([
                ['name' => $trueLabel, 'label' => $trueLabel, 'value' => 1],
                ['name' => $falseLabel, 'label' => $falseLabel, 'value' => 0],
            ], searchField: 'label', valueField: 'value'),
        ];
    }
}
