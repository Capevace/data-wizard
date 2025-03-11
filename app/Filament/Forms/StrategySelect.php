<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Select;
use Mateffy\Magic;

class StrategySelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('LLM Strategy')
            ->translateLabel()
            ->selectablePlaceholder(false)
            ->placeholder('Select a strategy')
            ->default('simple')
            ->native(false)
            ->options(
                Magic::getExtractionStrategies()
                    ->map(fn (string $strategyClass, string $strategy) => $strategyClass::getLabel())
                    ->all()
            );
    }
}
