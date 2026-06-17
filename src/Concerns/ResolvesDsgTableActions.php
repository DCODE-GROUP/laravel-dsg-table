<?php

namespace Dcodegroup\LaravelDsgTable\Concerns;

use Dcodegroup\LaravelDsgTable\Facades\DsgTable;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin JsonResource */
trait ResolvesDsgTableActions
{
    /**
     * @return array<string, array<string, mixed>>
     */
    protected function dsgTableActions(mixed $param = null): array
    {
        return DsgTable::get(static::dsgTableName())->actionsFor($this->resource, $param);
    }

    abstract protected static function dsgTableName(): string;
}
