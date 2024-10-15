<?php

namespace Mateffy\Magic\Prompt\Reflection;

use Attribute;

#[Attribute]
class Max extends PromptReflectionAttribute
{
    public function __construct(public mixed $value) {}

    public function getValidationRules(): array
    {
        return ['max:'.$this->value];
    }
}
