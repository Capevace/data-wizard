<?php

namespace Capevace\MagicImport\Prompt\Reflection;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_FUNCTION | Attribute::TARGET_CLASS)]
class PromptReflectionAttribute
{
    public function getValidationRules(): array
    {
        return [];
    }
}
