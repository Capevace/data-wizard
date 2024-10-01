<?php

namespace App\Models\SavedExtractor;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RedirectMode: string implements HasLabel, HasIcon
{
    case Redirect = 'redirect';
    case OpenNewTab = 'open_new_tab';


    public function getIcon(): ?string
    {
        return match ($this) {
            self::Redirect => 'bi-send',
            self::OpenNewTab => 'bi-window-plus',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Redirect => 'Redirect in same tab',
            self::OpenNewTab => 'Open in a new tab',
        };
    }
}
