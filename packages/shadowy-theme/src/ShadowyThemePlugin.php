<?php

namespace Mateffy\FilaTheme\Shadowy;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class ShadowyThemePlugin implements Plugin
{

    public function getId(): string
    {
        return 'shadowy';
    }

    public function register(Panel $panel): void
    {
        // Register views in:
        $path = base_path('packages/shadowy-theme/resources/views');
        View::addNamespace('filatheme', $path);
//        Blade::anonymousComponentPath($path, 'filatheme');

        $panel
            ->topNavigation(false)
            ->viteTheme(base_path('packages/shadowy-theme/resources/css/theme.css'));
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_START,
            fn () => view('filatheme::shadowy.sidebar-search')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn () => view('filatheme::shadowy.sidebar-profile')
        );
    }

}
