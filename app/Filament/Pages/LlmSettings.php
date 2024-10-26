<?php

namespace App\Filament\Pages;

use App\Filament\Pages\LlmSettings\AddApiCredentialsAction;
use App\Filament\Pages\LlmSettings\ApiConnectionDTO;
use App\Filament\Pages\LlmSettings\DeleteApiCredentialsAction;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Mateffy\Magic\Providers\ApiKey;
use Mateffy\Magic\Providers\ApiKey\ApiKeyProvider;

class LlmSettings extends Page
{
    protected static ?string $slug = 'llm-settings';

    protected static string $view = 'filament.pages.llm-settings';

    public static function getNavigationLabel(): string
    {
        return __('LLM Settings');
    }

    public function getTitle(): string|Htmlable
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationItems(): array
    {
        if (! config('app.allow_custom_user_keys')) {
            return [];
        }

        return parent::getNavigationItems();
    }

    #[Computed]
    public function apiKeys(): Collection
    {
        return auth()->user()
            ->apiKeys()
            ->get()
            ->groupBy('provider')
            ->mapWithKeys(fn (Collection $apiKeys, string $provider) => [
                $provider => ApiConnectionDTO::withKeys(
                    provider: ApiKeyProvider::from($provider),
                    apiKeys: $apiKeys
                ),
            ]);
    }

    public function mount(): void
    {
        if (! config('app.allow_custom_user_keys')) {
            abort(404);
        }

        $apiKeys = auth()->user()
            ->apiKeys()
            ->get();

        $this->form->fill(
            collect(ApiKeyProvider::cases())
                ->mapWithKeys(fn (ApiKeyProvider $provider) => [
                    $provider->getLabel() => $apiKeys
                        ->mapWithKeys(fn (ApiKey $apiKey) => [
                            $apiKey->type->value => $apiKey->secret,
                        ]),
                ])
                ->all()
        );
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([
                ...$this->apiKeys,
            ])
            ->schema([
                Group::make()
                    ->extraAttributes(['class' => '[&_.fi-fo-component-ctn]:gap-0'])
                    ->schema([
                        ...collect(ApiKeyProvider::cases())
                            ->map(fn (ApiKeyProvider $provider) => Section::make($provider->getLabel())
                                ->description($provider->getDescription())
                                ->statePath($provider->value)
                                ->icon($provider->getIcon())
                                ->iconSize('w-10 h-10')
                                ->iconColor(fn (?ApiConnectionDTO $state) => $state?->active ? 'success' : 'danger')
                                ->headerActions(array_filter([
                                    fn (?ApiConnectionDTO $state) => $state?->active
                                        ? DeleteApiCredentialsAction::make()
                                            ->apiConnection($state)
                                            ->after(function () {
                                                unset($this->apiKeys);
                                            })
                                        : AddApiCredentialsAction::make()
                                            ->color('gray')
                                            ->size('sm')
                                            ->initialProvider($provider)
                                            ->disabledProviders($this->apiKeys->pluck('provider')->all()),
                                ]))
                                ->extraAttributes([
                                    'class' => 'mb-5',
                                    'x-show' => '!search || \''.Str::lower($provider->getLabel()).'\'.includes(search.toLowerCase())',
                                    'x-bind:class' => "{ 'hidden': !(!search || '".Str::lower($provider->getLabel())."'.includes(search.toLowerCase())) }",
                                ])
                                ->schema(fn () => match ($provider) {
                                    ApiKeyProvider::OpenAI => [
                                        TextEntry::make('fields.token')
                                            ->label('API Token')
                                            ->translateLabel()
                                            ->placeholder(__('Not configured'))
                                            ->fontFamily('mono')
                                            ->size('xs')
                                            ->color('gray'),
                                        TextEntry::make('fields.organization')
                                            ->label('Organization')
                                            ->translateLabel()
                                            ->placeholder('No organization specified')
                                            ->fontFamily('mono')
                                            ->size('xs')
                                            ->color('gray'),
                                    ],
                                    default => [
                                        TextEntry::make('fields.token')
                                            ->label('API Token')
                                            ->translateLabel()
                                            ->placeholder(__('Not configured'))
                                            ->fontFamily('mono')
                                            ->size('xs')
                                            ->color('gray'),
                                    ],
                                }),
                            )
                            ->all(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

    }
}
