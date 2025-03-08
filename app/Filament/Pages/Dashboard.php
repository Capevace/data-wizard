<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $view = 'components.dashboard';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }
}
