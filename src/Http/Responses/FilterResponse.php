<?php

namespace Dcodegroup\LaravelDsgTable\Http\Responses;

use Illuminate\Http\JsonResponse;

class FilterResponse extends JsonResponse
{
    /**
     * @param  array<int, array<string, mixed>>  $filters
     */
    public static function make(array $filters, int $status = 200, array $headers = []): self
    {
        return new self($filters, $status, $headers);
    }
}
