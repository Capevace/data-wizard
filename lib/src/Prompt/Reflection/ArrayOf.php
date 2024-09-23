<?php

namespace Mateffy\Magic\Prompt\Reflection;

use Attribute;

#[Attribute]
class ArrayOf extends PromptReflectionAttribute
{
    public function __construct(public string $type) {}

    public function getValidationRules(): array
    {
        return ['array'];
    }
}
