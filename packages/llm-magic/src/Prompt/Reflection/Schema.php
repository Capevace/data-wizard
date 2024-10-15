<?php

namespace Mateffy\Magic\Prompt\Reflection;

use Attribute;

#[Attribute]
class Schema extends PromptReflectionAttribute
{
    public function __construct(public array|string $schemaOrClasspath) {}
}
