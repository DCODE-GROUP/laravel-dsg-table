<?php

namespace Dcodegroup\LaravelDsgTable\Facets;

use Dcodegroup\LaravelDsgTable\Contracts\Facetable;

class Facet
{
    /**
     * @return array<string, mixed>
     */
    public static function build(Facetable $facet): array
    {
        return $facet->toFilterDefinition();
    }

    /**
     * @param  array<int, Facetable>  $facets
     * @return array<int, array<string, mixed>>
     */
    public static function collection(array $facets): array
    {
        return array_map(fn (Facetable $facet) => static::build($facet), $facets);
    }
}
