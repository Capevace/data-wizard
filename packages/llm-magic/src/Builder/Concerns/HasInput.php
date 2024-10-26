<?php

namespace Mateffy\Magic\Builder\Concerns;

use Closure;
use Illuminate\Container\Container;

trait HasInput
{
    public Closure|string|null $input = null;

    public function input(Closure|string $input): static
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): ?string
    {
        if ($this->input instanceof Closure) {
            return Container::getInstance()->call($this->input);
        }

        return $this->input;
    }
}
