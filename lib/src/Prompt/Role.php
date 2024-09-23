<?php

namespace Mateffy\Magic\Prompt;

enum Role: string
{
    case System = 'system';
    case Assistant = 'assistant';
    case User = 'user';
}
