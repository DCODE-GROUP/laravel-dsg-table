<?php

namespace Dcodegroup\LaravelDsgTable\Actions;

class RowActions
{
    /** @var array<string, array<string, mixed>> */
    protected array $actions = [];

    public function __construct(
        protected mixed $model,
        protected mixed $param = null,
    ) {}

    public static function for(mixed $model, mixed $param = null): static
    {
        return new static($model, $param);
    }

    public function view(
        string $routeName,
        mixed $parameters = null,
        ?string $label = null,
    ): static {
        $this->actions['view'] = TableAction::link(
            $routeName,
            $parameters ?? $this->model,
            $label ?? __(config('dsg-table.actions.labels.view')),
        );

        return $this;
    }

    public function edit(
        string $routeName,
        mixed $parameters = null,
        ?string $label = null,
    ): static {
        $this->actions['edit'] = TableAction::link(
            $routeName,
            $parameters ?? $this->model,
            $label ?? __(config('dsg-table.actions.labels.edit')),
        );

        return $this;
    }

    public function delete(
        string $routeName,
        mixed $parameters = null,
        ?string $label = null,
        ?string $content = null,
        ?string $component = null,
    ): static {
        $this->actions['delete'] = TableAction::delete(
            $routeName,
            $parameters ?? $this->model,
            $label,
            $content,
            $component,
        );

        return $this;
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    public function add(string $key, array $definition): static
    {
        $this->actions[$key] = $definition;

        return $this;
    }

    public function when(bool $condition, callable $callback): static
    {
        if ($condition) {
            $callback($this);
        }

        return $this;
    }

    public function unless(bool $condition, callable $callback): static
    {
        return $this->when(! $condition, $callback);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->actions;
    }
}
