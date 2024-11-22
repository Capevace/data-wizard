<?php

namespace Mateffy\Magic\Prompt;

enum Role: string
{
    case System = 'system';
    case Assistant = 'assistant';
    case User = 'user';

    public function getLabel(): string
    {
        return match ($this) {
            self::System => 'System',
            self::Assistant => 'Assistant',
            self::User => 'User',
        };
    }
}
