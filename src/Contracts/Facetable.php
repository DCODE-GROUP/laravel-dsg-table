<?php

namespace Dcodegroup\LaravelDsgTable\Contracts;

interface Facetable
{
    /**
     * @return array<string, mixed>
     */
    public function toFilterDefinition(): array;
}
