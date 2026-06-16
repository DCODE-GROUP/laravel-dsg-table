<?php

namespace Dcodegroup\LaravelDsgTable\Exceptions;

use Dcodegroup\LaravelDsgTable\Contracts\TableInterface;
use Exception;

class TableException extends Exception
{
    public static function interfaceNotImplemented(string $class): static
    {
        return new static("Table class `{$class}` does not implement ".TableInterface::class.'.');
    }
}
