<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables;

use Dcodegroup\LaravelDsgTable\Actions\CrudActions;
use Dcodegroup\LaravelDsgTable\Columns\Column;
use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Dcodegroup\LaravelDsgTable\Facets\BooleanFacet;
use Dcodegroup\LaravelDsgTable\Facets\DateRangeFacet;
use Dcodegroup\LaravelDsgTable\Facets\Facet;
use Dcodegroup\LaravelDsgTable\Filters\FilterBuilder;
use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Resources\UsersTableResource;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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

        return UsersTableResource::collection(collect([
            (object) ['id' => 1, 'name' => 'Jane Doe'],
            (object) ['id' => 2, 'name' => 'John Smith'],
        ]));
    }

    public function fields(): Collection
    {
        return collect([
            Column::make('name', 'Name')->toArray(),
            Column::make('email', 'Email')->isSortable()->toArray(),
        ]);
    }

    public function filters(Request $request, mixed $param = null): array
    {
        return array_merge(
            FilterBuilder::make()
                ->refineItems('active', 'Status', [
                    ['name' => 'Active', 'value' => 1],
                    ['name' => 'Inactive', 'value' => 0],
                ], valueField: 'value', apiMode: false)
                ->toArray(),
            Facet::collection([
                DateRangeFacet::make('Created', 'created_at'),
                BooleanFacet::make('Published', 'published'),
            ]),
        );
    }

    public function actionsFor(mixed $model, mixed $param = null): array
    {
        return CrudActions::for($model, 'admin.users', $param)
            ->withView()
            ->withEdit()
            ->withDelete('Are you sure you want to delete this user?')
            ->toArray();
    }
}
