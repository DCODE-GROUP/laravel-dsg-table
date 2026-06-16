<?php

namespace Dcodegroup\LaravelDsgTable\Support;

use Illuminate\Support\Collection;

class ActiveFilter
{
    public static function items(): Collection
    {
        $activeLabel = __(config('dsg-table.active_filter.active_label'));
        $inactiveLabel = __(config('dsg-table.active_filter.inactive_label'));

        return collect([
            [
                'name' => $activeLabel,
                'value' => 1,
            ],
            [
                'name' => $inactiveLabel,
                'value' => 0,
            ],
        ]);
    }
}
