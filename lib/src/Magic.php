<?php

namespace Capevace\MagicImport;

use Capevace\MagicImport\Builder\ChatPreconfiguredModelBuilder;
use Capevace\MagicImport\Builder\ExtractionLLMBuilder;

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
