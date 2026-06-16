<?php

namespace Dcodegroup\LaravelDsgTable\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TableNotFoundException extends NotFoundHttpException
{
    public static function make(string $class, string $tableName): self
    {
        return new self("Table class `{$class}` was not found for table name: {$tableName}.");
    }
}
