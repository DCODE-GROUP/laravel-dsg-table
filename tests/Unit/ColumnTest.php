<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Columns\ActionsColumn;
use Dcodegroup\LaravelDsgTable\Columns\Column;
use Dcodegroup\LaravelDsgTable\Columns\SlotColumn;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;

class ColumnTest extends TestCase
{
    public function test_column_builds_a_basic_definition_with_configured_default_data_class(): void
    {
        config(['dsg-table.columns.data_class.default' => 'cell-default']);

        $column = Column::make('email', 'Email address')->toArray();

        $this->assertSame([
            'name' => 'email',
            'title' => 'Email address',
            'dataClass' => 'cell-default',
        ], $column);
    }

    public function test_column_supports_sorting_width_and_type(): void
    {
        $column = Column::make('updated_at')
            ->isSortable(sortField: 'updated_at')
            ->width('180px')
            ->type('date')
            ->style('white-space: nowrap')
            ->toArray();

        $this->assertSame('updated_at', $column['sortField']);
        $this->assertSame('180px', $column['width']);
        $this->assertSame('date', $column['type']);
        $this->assertSame('white-space: nowrap', $column['style']);
    }

    public function test_column_data_class_can_be_replaced_appended_or_removed(): void
    {
        $column = Column::make('name', dataClass: 'base');

        $column->appendDataClass('font-medium');
        $this->assertSame('base font-medium', $column->toArray()['dataClass']);

        $column->dataClass('replacement');
        $this->assertSame('replacement', $column->toArray()['dataClass']);

        $column->withoutDataClass();
        $this->assertArrayNotHasKey('dataClass', $column->toArray());
    }

    public function test_column_omits_data_class_when_config_default_is_null(): void
    {
        config(['dsg-table.columns.data_class.default' => null]);

        $column = Column::make('name')->toArray();

        $this->assertArrayNotHasKey('dataClass', $column);
    }

    public function test_slot_column_uses_slot_default_data_class_and_field(): void
    {
        config(['dsg-table.columns.data_class.slot' => 'slot-default']);

        $column = SlotColumn::make('active', 'Status')->toArray();

        $this->assertSame([
            'name' => 'active',
            'title' => 'Status',
            'field' => 'active',
            'dataClass' => 'slot-default',
        ], $column);
    }

    public function test_actions_column_uses_actions_default_data_class_and_width(): void
    {
        config(['dsg-table.columns.data_class.actions' => 'actions']);

        $column = ActionsColumn::make(width: '120px')->toArray();

        $this->assertSame([
            'name' => 'actions',
            'title' => '',
            'dataClass' => 'actions',
            'width' => '120px',
        ], $column);
    }
}
