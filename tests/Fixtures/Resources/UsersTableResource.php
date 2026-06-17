<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Fixtures\Resources;

use Dcodegroup\LaravelDsgTable\Concerns\ResolvesDsgTableActions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersTableResource extends JsonResource
{
    use ResolvesDsgTableActions;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'actions' => $this->dsgTableActions(),
        ];
    }

    protected static function dsgTableName(): string
    {
        return 'users';
    }
}
