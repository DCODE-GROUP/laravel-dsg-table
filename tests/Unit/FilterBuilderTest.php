<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Facets\BooleanFacet;
use Dcodegroup\LaravelDsgTable\Facets\DateRangeFacet;
use Dcodegroup\LaravelDsgTable\Facets\SelectFacet;
use Dcodegroup\LaravelDsgTable\Filters\FilterBuilder;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;

class FilterBuilderTest extends TestCase
{
    public function test_refine_items_builds_dsg_refines_shape(): void
    {
        $filters = FilterBuilder::make()
            ->refineItems('active', 'Status', [
                ['label' => 'Active', 'value' => 1],
                ['label' => 'Inactive', 'value' => 0],
            ], searchField: 'label', valueField: 'value', apiMode: false)
            ->toArray();

        $this->assertSame([
            'key' => 'active',
            'name' => 'Status',
            'api_mode' => false,
            'type' => 'refines',
            'refines' => [
                ['label' => 'Active', 'value' => 1, 'selected' => false, 'visible' => true],
                ['label' => 'Inactive', 'value' => 0, 'selected' => false, 'visible' => true],
            ],
        ], $filters[0]);
    }

    public function test_date_range_facet_outputs_dsg_date_range_type(): void
    {
        $definition = DateRangeFacet::make('Created', 'created_at')->toFilterDefinition();

        $this->assertSame('date_range', $definition['type']);
        $this->assertSame('created_at', $definition['key']);
    }

    public function test_boolean_facet_outputs_refines_with_translated_labels(): void
    {
        $definition = BooleanFacet::make('Active', 'active')->toFilterDefinition();

        $this->assertSame('refines', $definition['type']);
        $this->assertSame(__('dsg-table::filters.yes'), $definition['refines'][0]['label']);
        $this->assertSame(__('dsg-table::filters.no'), $definition['refines'][1]['label']);
    }

    public function test_boolean_facet_accepts_custom_labels(): void
    {
        $definition = BooleanFacet::make(
            'Status',
            'active',
            trueLabel: 'Active',
            falseLabel: 'Inactive',
        )->toFilterDefinition();

        $this->assertSame('Active', $definition['refines'][0]['label']);
        $this->assertSame('Inactive', $definition['refines'][1]['label']);
    }

    public function test_select_facet_outputs_refines_from_items(): void
    {
        $definition = SelectFacet::make('Role', 'role', [
            ['label' => 'Admin', 'value' => 'admin'],
        ])->toFilterDefinition();

        $this->assertSame('admin', $definition['refines'][0]['value']);
    }
}
