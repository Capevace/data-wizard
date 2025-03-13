<?php

namespace App\Filament\Resources\ExtractionBucketResource\Actions;

use App\Models\ExtractionRun;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class BulkRetryExtractionAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('retryExtraction')
            ->label('Retry Extraction')
            ->icon('heroicon-o-arrow-path-rounded-square')
            ->action(function (Collection $records) {
                /** @var Collection<ExtractionRun> $records */

                $records->each(function (ExtractionRun $record) {
                    $record->retry();
                });
            });
    }
}
