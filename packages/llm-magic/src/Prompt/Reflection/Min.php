<?php

namespace Mateffy\Magic\Prompt\Reflection;

use Attribute;

#[Attribute]
class Min extends PromptReflectionAttribute
{
    public function __construct(public int $value) {}

    public function getValidationRules(): array
    {
        return ['min:'.$this->value];
    }
}
