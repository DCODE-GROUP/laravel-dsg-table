<?php

namespace Dcodegroup\LaravelDsgTable\Contracts;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

interface TableInterface
{
    /**
     * Authorize access to this table for the current user.
     */
    public function authorisation(mixed $arguments = null): Response|bool;

    /**
     * Build and return the paginated resource collection for this table.
     */
    public function resourceCollection(mixed $param = null): AnonymousResourceCollection;

    /**
     * Return the column definitions for the table header.
     */
    public function fields(): Collection;
}
