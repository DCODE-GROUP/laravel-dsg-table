<?php

namespace Dcodegroup\LaravelDsgTable\Columns\Concerns;

trait ConfiguresDataClass
{
    protected static function defaultDataClass(string $type = 'default'): ?string
    {
        return config("dsg-table.columns.data_class.{$type}");
    }

    public function dataClass(?string $dataClass): static
    {
        if ($dataClass === null) {
            unset($this->column['dataClass']);
        } else {
            $this->column['dataClass'] = $dataClass;
        }

        return $this;
    }

    public function appendDataClass(string $dataClass): static
    {
        $existing = $this->column['dataClass'] ?? '';

        $this->column['dataClass'] = trim("{$existing} {$dataClass}");

        return $this;
    }

    public function withoutDataClass(): static
    {
        unset($this->column['dataClass']);

        return $this;
    }
}
