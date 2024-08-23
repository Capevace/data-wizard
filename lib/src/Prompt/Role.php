<?php

namespace Capevace\MagicImport\Prompt;

enum Role: string
{
    case System = 'system';
    case Assistant = 'assistant';
    case User = 'user';
}
