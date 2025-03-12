<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ApiTokenSettings;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\GeneratePage;
use App\Filament\Pages\LlmSettings;
use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionRunResource;
use App\Filament\Resources\SavedExtractorResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Actions\Action;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        FilamentAsset::register([
            AlpineComponent::make('GeneratorComponent', resource_path('js/dist/components/generator.js')),
        ]);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->spa()
            ->default()
            ->id('app')
            ->path('app')
            ->brandName('Data Wizard 🪄')
            ->topNavigation()
            ->navigation(fn (NavigationBuilder $builder) => $builder
                ->items([
                    ...Dashboard::getNavigationItems(),
                    ...ExtractionBucketResource::getNavigationItems(),
                    ...SavedExtractorResource::getNavigationItems(),
                    ...ExtractionRunResource::getNavigationItems(),
                ])
                ->group(
                    NavigationGroup::make(__('Settings'))
                        ->icon('heroicon-o-cog')
                        ->items([
                            ...LlmSettings::getNavigationItems(),
                            ...ApiTokenSettings::getNavigationItems(),
                        ])
                )
            )
            ->userMenuItems([
                MenuItem::make()
                    ->label('API Documentation')
                    ->icon('heroicon-o-code-bracket')
                    ->color('gray')
                    ->url(url('/api'), shouldOpenInNewTab: true),
            ])
            ->login()
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('3.7rem')
            ->brandName(new HtmlString('Data Wizard 🪄'))
            ->favicon('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🪄</text></svg>')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->font('Asap')
            ->globalSearch(false)
            ->colors([
                'primary' => json_decode(<<<JSON
                {
                    "50": "#EDF4F7",
                    "100": "#DFEBF1",
                    "200": "#C0D7E3",
                    "300": "#9CC1D3",
                    "400": "#7DADC5",
                    "500": "#5D99B6",
                    "600": "#47819E",
                    "700": "#366278",
                    "800": "#233F4D",
                    "900": "#112027",
                    "950": "#091115"
                }
                JSON, true, 512, JSON_THROW_ON_ERROR),
                'gray' => Color::Zinc,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->bootUsing(function () {
                FilamentView::registerRenderHook(
                    PanelsRenderHook::SCRIPTS_BEFORE,
                    fn () => Blade::render("@vite(['resources/js/src/magic-extract.ts'])")
                );

                Action::configureUsing(function (Action $action) {
                    $action
                        ->modalFooterActionsAlignment(Alignment::Right);
                });

                FormAction::configureUsing(function (FormAction $action) {
                    $action
                        ->modalFooterActionsAlignment(Alignment::Right);
                });

                TableAction::configureUsing(function (TableAction $action) {
                    $action
                        ->modalFooterActionsAlignment(Alignment::Right);
                });

                InfolistAction::configureUsing(function (InfolistAction $action) {
                    $action
                        ->modalFooterActionsAlignment(Alignment::Right);
                });
            });
    }
}
