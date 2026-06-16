<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Support\ActiveFilter;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;

class ActiveFilterTest extends TestCase
{
    public function test_items_returns_active_and_inactive_options(): void
    {
        $items = ActiveFilter::items();

        $this->assertCount(2, $items);
        $this->assertSame(__('dsg-table::filters.active'), $items[0]['name']);
        $this->assertSame(1, $items[0]['value']);
        $this->assertSame(__('dsg-table::filters.inactive'), $items[1]['name']);
        $this->assertSame(0, $items[1]['value']);
    }
}
