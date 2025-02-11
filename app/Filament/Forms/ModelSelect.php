<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Select;
use Mateffy\Magic;
use Mateffy\Magic\Providers\ApiKey\ApiKeyProvider;

class ModelSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Model')
            ->placeholder(fn () => ($model = config('magic-extract.default-model'))
                ? __('Global default') . ': ' . Magic::defaultModelLabel()
                : __('Select a model')
            )
            ->searchable()
            ->translateLabel()
            ->hintIcon(fn (?string $state) => $state && ($provider = ApiKeyProvider::tryFromModelString($state))
                ? $provider->getIcon()
                : ApiKeyProvider::default()?->getIcon() ?? 'bi-robot'
            )
            ->hintColor('primary')
            ->hint(fn (?string $state) => $state && ($provider = ApiKeyProvider::tryFromModelString($state))
                ? $provider->getLabel()
                : ApiKeyProvider::default()?->getLabel()
            )
            ->options(Magic::models());
    }
}
