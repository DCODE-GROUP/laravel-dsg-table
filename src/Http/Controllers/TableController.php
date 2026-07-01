<?php

namespace Dcodegroup\LaravelDsgTable\Http\Controllers;

use Dcodegroup\LaravelDsgTable\Facades\DsgTable;
use Dcodegroup\LaravelDsgTable\Http\Resources\DsgTableResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function __invoke(Request $request, string $tableName, mixed $param = null): DsgTableResourceCollection
    {
        $table = DsgTable::get($tableName);

        $table->authorisation($param);

        return DsgTableResourceCollection::forTable(
            $table->resourceCollection($param),
            $table,
            $param,
        );
    }
}
