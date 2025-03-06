<?php

namespace App\Filament\Forms;

use App\Models\ApiKey\ApiKeyProvider;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Mateffy\Magic;

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
            ->extraAttributes(['class' => new HtmlString('[&_.choices\_\_inner]:flex [&_.choices\_\_inner]:items-center [&_.choices\_\_inner]:h-9')])
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
            ->getSearchResultsUsing(fn (string $search) => Magic::models()
                ->filter(fn ($label, $key) => Str::contains(Str::lower($label), Str::lower($search)))
                ->mapWithKeys(fn ($label, $key) => [$key => $this->renderModelItem($key, $label)])
            )
            ->searchDebounce(100)
            ->allowHtml()
            ->rule("in:" . Magic::models()->keys()->implode(','))
            ->options(
                Magic::models()
                    ->mapWithKeys(fn ($label, $key) => [$key => $this->renderModelItem($key, $label)])
            );
    }

    protected function renderModelItem(string $key, string $label): string
    {
        $icon = ApiKeyProvider::tryFromModelString($key)?->getIcon() ?? 'bi-robot';

        return view('components.forms.model-select-item', [
            'key' => $key,
            'label' => $label,
            'icon' => $icon,
        ])->render();
    }
}
