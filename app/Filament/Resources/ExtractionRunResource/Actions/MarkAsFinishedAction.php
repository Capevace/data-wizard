<?php

namespace App\Filament\Resources\ExtractionRunResource\Actions;

use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use Filament\Actions\Action;

class MarkAsFinishedAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('markAsFinished')
            ->label('Mark as finished')
            ->tooltip('If the extraction is still running, this will not stop it, but will mark it as finished.')
            ->translateLabel()
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->hidden(fn (ExtractionRun $record) => $record->status === RunStatus::Completed || $record->status === RunStatus::Failed || $record->finished_at !== null)
            ->action(fn (ExtractionRun $record) => $record->update(['status' => RunStatus::Completed, 'finished_at' => now()]));
    }
}
