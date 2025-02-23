<?php

namespace App\Filament\Pages;

use Illuminate\Contracts\View\View;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $view = 'components.dashboard';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }
}
