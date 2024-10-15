<?php

namespace Mateffy\Magic\Prompt\Reflection;

use Attribute;

#[Attribute]
class Description extends PromptReflectionAttribute
{
    public function __construct(public string $value) {}

    public function getValidationRules(): array
    {
        return [];
    }
}
