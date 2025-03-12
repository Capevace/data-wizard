<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Filament\Forms\iFrameField;
use App\Livewire\Components\EmbeddedExtractor\ExtractorSteps;
use App\Livewire\Components\EmbeddedExtractor\WidgetAlignment;
use App\Models\SavedExtractor;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ConfigureIFrameAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $makeUrl = function (SavedExtractor $record, $step, $horizontalAlignment, $verticalAlignment) {
            $step = ExtractorSteps::tryFrom($step);
            $horizontalAlignment = WidgetAlignment::tryFrom($horizontalAlignment);
            $verticalAlignment = WidgetAlignment::tryFrom($verticalAlignment);

            return $record->getEmbeddedUrl(
                step: $step,
                horizontalAlignment: $horizontalAlignment,
                verticalAlignment: $verticalAlignment,
            );
        };

        $this
            ->name('configureIFrame')
            ->label('Configure iFrame')
            ->icon('bi-code-slash')
            ->modalWidth('full')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalHeading('Configure iFrame')
            ->modalDescription('Configure the iFrame for embedding the extractor')
            ->modalIcon('bi-code-slash')
            ->modalSubmitAction(false)
            ->modalCancelAction(fn (StaticAction $action) => $action
                ->label('Close')
                ->icon('heroicon-o-arrow-left')
            )
            ->fillForm(fn (SavedExtractor $record, Get $get) => [
                'url' => $makeUrl($record, ExtractorSteps::Introduction->value, WidgetAlignment::Center->value, WidgetAlignment::Center->value),
                'step' => ExtractorSteps::Introduction->value,
                'horizontalAlignment' => WidgetAlignment::Center->value,
                'verticalAlignment' => WidgetAlignment::Center->value,
            ])
//            step, horizontalAlignment, verticalAlignment
            ->form([
                Grid::make(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                iFrameField::make('url')
                                    ->extraAttributes(['class' => 'aspect-video'])
                                    ->zoom(0.5)
                                    ->label('URL'),
                            ]),

                        Group::make()
                            ->live()
                            ->afterStateUpdated(function (SavedExtractor $record, Get $get, Set $set) use ($makeUrl) {
                                $url = $makeUrl(
                                    $record,
                                    $get('skipIntroduction')
                                        ? ExtractorSteps::Bucket->value
                                        : ExtractorSteps::Introduction->value,
                                    $get('horizontalAlignment'),
                                    $get('verticalAlignment')
                                );

                                $set('url', $url);
                            })
                            ->schema([
                                TextInput::make('url')
                                    ->label('URL')
                                    ->disabled(),

                                Fieldset::make()
                                    ->label('Settings')
                                    ->schema([
                                        Toggle::make('skipIntroduction')
                                            ->columnSpanFull()
                                            ->label('Skip Introduction'),

                                        Select::make('horizontalAlignment')
                                            ->live()
                                            ->options(WidgetAlignment::class),

                                        Select::make('verticalAlignment')
                                            ->live()
                                            ->options(WidgetAlignment::class),
                                    ])
                            ])
                    ])
            ]);
    }
}
