<?php

namespace Dcodegroup\LaravelDsgTable\Http\Controllers;

use Dcodegroup\LaravelDsgTable\Facades\DsgTable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function __invoke(Request $request, string $tableName, mixed $param = null): AnonymousResourceCollection
    {
        $table = DsgTable::get($tableName);

        $table->authorisation($param);

        return $table->resourceCollection($param);
    }
}
