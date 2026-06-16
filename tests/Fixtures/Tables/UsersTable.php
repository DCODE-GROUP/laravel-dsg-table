<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables;

use Dcodegroup\LaravelDsgTable\Columns\Column;
use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class UsersTable implements TableInterface
{
    public static mixed $authorisedWith = null;

    public static mixed $collectionParam = null;

    public function authorisation(mixed $arguments = null): Response|bool
    {
        self::$authorisedWith = $arguments;

        return true;
    }

    public function resourceCollection(mixed $param = null): AnonymousResourceCollection
    {
        self::$collectionParam = $param;

        return JsonResource::collection(collect([
            ['id' => 1, 'name' => 'Jane Doe'],
            ['id' => 2, 'name' => 'John Smith'],
        ]));
    }

    public function fields(): Collection
    {
        return collect([
            Column::make('name', 'Name')->toArray(),
            Column::make('email', 'Email')->isSortable()->toArray(),
        ]);
    }
}
