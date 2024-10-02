<?php

namespace Mateffy\Magic;

use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\Builder\ExtractionLLMBuilder;
use Mateffy\Magic\Loop\EndConversation;

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

    public static function end(mixed $output): EndConversation
    {
        return new EndConversation($output);
    }

    public static function function(string $description, array $schema): ExtractionLLMBuilder
    {
        return new ExtractionLLMBuilder;
    }
}
