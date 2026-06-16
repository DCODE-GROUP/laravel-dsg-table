<?php

namespace Dcodegroup\LaravelDsgTable\Http\Controllers;

use Dcodegroup\LaravelDsgTable\Facades\DsgTable;
use Dcodegroup\LaravelDsgTable\Http\Responses\FilterResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableFiltersController extends Controller
{
    public function __invoke(Request $request, string $tableName, mixed $param = null): FilterResponse
    {
        $table = DsgTable::get($tableName);

        $table->authorisation($param);

        return FilterResponse::make($table->filters($request, $param));
    }
}
