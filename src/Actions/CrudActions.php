<?php

namespace Dcodegroup\LaravelDsgTable\Actions;

class CrudActions
{
    public function __construct(
        protected RowActions $rowActions,
        protected string $routePrefix,
    ) {}

    public static function for(mixed $model, string $routePrefix, mixed $param = null): static
    {
        return new static(RowActions::for($model, $param), $routePrefix);
    }

    public function withView(?string $label = null): static
    {
        $this->rowActions->view("{$this->routePrefix}.show", label: $label);

        return $this;
    }

    public function withEdit(?string $label = null): static
    {
        $this->rowActions->edit("{$this->routePrefix}.edit", label: $label);

        return $this;
    }

    public function withDelete(?string $content = null, ?string $label = null, ?string $component = null): static
    {
        $this->rowActions->delete(
            "{$this->routePrefix}.destroy",
            content: $content,
            label: $label,
            component: $component,
        );

        return $this;
    }

    public function when(bool $condition, callable $callback): static
    {
        $this->rowActions->when($condition, $callback);

        return $this;
    }

    public function unless(bool $condition, callable $callback): static
    {
        $this->rowActions->unless($condition, $callback);

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->rowActions->toArray();
    }

    public function rowActions(): RowActions
    {
        return $this->rowActions;
    }
}
