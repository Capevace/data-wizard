<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Select;

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
            ->options([
                'simple' => __('Simple'),
                'sequential' => __('Sequential'),
                'parallel' => __('Parallel'),
            ]);
    }
}
