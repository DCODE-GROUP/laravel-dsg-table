<?php

namespace Dcodegroup\LaravelDsgTable\Actions;

class TableAction
{
    /**
     * @return array{link: string, label: string}
     */
    public static function link(
        string $routeName,
        mixed $parameters = [],
        ?string $label = null,
    ): array {
        return [
            'link' => route($routeName, static::resolveParameters($parameters)),
            'label' => $label ?? '',
        ];
    }

    /**
     * @return array{link: string, label: string, component?: string, content?: string}
     */
    public static function delete(
        string $routeName,
        mixed $parameters = [],
        ?string $label = null,
        ?string $content = null,
        ?string $component = null,
    ): array {
        $action = [
            'link' => route($routeName, static::resolveParameters($parameters)),
            'label' => $label ?? __(config('dsg-table.actions.labels.delete')),
        ];

        $component ??= config('dsg-table.actions.delete.component');

        if ($component !== null && $component !== '') {
            $action['component'] = $component;
        }

        if ($content !== null && $content !== '') {
            $action['content'] = $content;
        }

        return $action;
    }

    public static function resolveParameters(mixed $parameters): mixed
    {
        if (is_array($parameters)) {
            return array_map([static::class, 'resolveParameters'], $parameters);
        }

        if (! is_object($parameters)) {
            return $parameters;
        }

        if (method_exists($parameters, 'getRouteKey')) {
            return $parameters;
        }

        if (isset($parameters->id)) {
            return $parameters->id;
        }

        return $parameters;
    }
}
