<?php

namespace Dcodegroup\LaravelDsgTable\Support;

use Illuminate\Support\Collection;

class RefineItems
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function from(
        iterable $items,
        string $searchField = 'name',
        string $valueField = 'id',
        bool $itemSelected = false,
    ): array {
        return Collection::make($items)->map(fn ($item) => [
            'label' => (string) data_get($item, $searchField),
            'value' => data_get($item, $valueField),
            'selected' => $itemSelected,
            'visible' => true,
        ])->values()->all();
    }
}
