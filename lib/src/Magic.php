<?php

namespace Mateffy\Magic;

use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\Builder\ExtractionLLMBuilder;

class Magic
{
    public static function extract(): ExtractionLLMBuilder
    {
        return new ExtractionLLMBuilder;
    }

    public static function chat(): ChatPreconfiguredModelBuilder
    {
        return new ChatPreconfiguredModelBuilder;
    }
}
