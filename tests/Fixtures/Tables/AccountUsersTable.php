<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables;

use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AccountUsersTable implements TableInterface
{
    public function authorisation(mixed $arguments = null): Response|bool
    {
        return true;
    }

    public function resourceCollection(mixed $param = null): AnonymousResourceCollection
    {
        return JsonResource::collection(collect([
            ['id' => $param, 'account_id' => $param],
        ]));
    }

    public function fields(): Collection
    {
        return collect([]);
    }

    public function filters(Request $request, mixed $param = null): array
    {
        return [];
    }

    public function actionsFor(mixed $model, mixed $param = null): array
    {
        return [];
    }
}
