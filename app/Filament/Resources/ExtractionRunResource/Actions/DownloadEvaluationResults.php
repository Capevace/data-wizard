<?php

namespace App\Filament\Resources\ExtractionRunResource\Actions;

use App\Models\ExtractionRun;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Mateffy\Magic\Models\ElElEm;

class DownloadEvaluationResults extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('downloadEvaluationResults')
            ->label('Download evaluation')
            ->translateLabel()
            ->icon('heroicon-o-beaker')
            ->action(function (ExtractionRun $record) {
                $options = $record->getContextOptions();
                $input_tokens = $record->bucket->calculateInputTokens($options);

                $results = [
                    'model' => $record->model,
                    'strategy' => $record->strategy,
                    'options' => $options->toArray(),
                    'bucket' => $record->bucket_id,
                    'input_tokens' => $input_tokens,
                    'input_cents' => ElElEm::fromString($record->model)->getModelCost()->inputCostInCents($input_tokens),
                    'duration_seconds' => $record->duration->totalSeconds,
                    'schema' => $record->target_schema,
                    'data' => $record->data,
                ];

                $model_slug = Str::slug($record->model);

                $slug = Str::slug($record->bucket->description) . "_{$record->strategy}_{$results['options']['type']}_{$model_slug}";

                $json = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

                return response()->streamDownload(function () use ($json) {
                    echo $json;
                }, "evaluation_{$slug}.json");
            });
    }


}
