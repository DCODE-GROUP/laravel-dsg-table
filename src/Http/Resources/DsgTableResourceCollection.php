<?php

namespace Dcodegroup\LaravelDsgTable\Http\Resources;

use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DsgTableResourceCollection extends AnonymousResourceCollection
{
    public function __construct(
        mixed $resource,
        ?string $collects,
        protected TableInterface $table,
        protected mixed $tableParam = null,
        protected string $actionsKey = 'actions',
    ) {
        parent::__construct($resource, $collects);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function (JsonResource $resource) use ($request) {
            $row = $resource->resolve($request);

            if (! array_key_exists($this->actionsKey, $row)) {
                $row[$this->actionsKey] = $this->table->actionsFor(
                    $resource->resource,
                    $this->tableParam,
                );
            }

            return $row;
        })->all();
    }

    public static function forTable(
        AnonymousResourceCollection $collection,
        TableInterface $table,
        mixed $tableParam = null,
        string $actionsKey = 'actions',
    ): self {
        return new self(
            $collection->resource,
            $collection->collects,
            $table,
            $tableParam,
            $actionsKey,
        );
    }
}
