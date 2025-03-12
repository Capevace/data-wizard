<?php

namespace App\Livewire\Components\EmbeddedExtractor;

enum WidgetAlignment: string
{
    case Start = 'start';
    case Center = 'center';
    case End = 'end';
    case Stretch = 'stretch';

    public function itemsClass(): string
    {
        return match ($this) {
            self::Start => 'items-start',
            self::Center => 'items-center',
            self::End => 'items-end',
            self::Stretch => 'items-stretch',
        };
    }
}
