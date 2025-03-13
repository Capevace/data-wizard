<?php

namespace App\Filament\Resources\ExtractionBucketResource\Actions;

use App\Filament\Forms\JsonEditor;
use App\Models\ExtractionRun;
use Filament\Forms\Components\View;
use Filament\Tables\Actions\ViewAction;

class QuickViewRunAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->slideOver()
            ->name('quickView')
            ->label('Quick View')
            ->fillForm(fn (ExtractionRun $run) => [
                'data' => $run->data,
            ])
            ->form(fn (ExtractionRun $record) => [
                JsonEditor::make('data')
                    ->required()
                    ->readOnly()
                    ->label('JSON Data')
                    ->translateLabel(),
//
//                View::make('view')
//                    ->view('llm-magic::components.json-schema')
//                    ->viewData([
//                        'schema' => $record->target_schema,
//                        'statePath' => '$wire.mountedTableActionsData.0.data'
//                    ])
            ])
            ->modalSubmitAction(false)
            ->action(fn () => null);
    }
}
