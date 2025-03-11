<?php

namespace App\Livewire\Components;

use App\Livewire\Components\Shared\HasPartialJson;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\SavedExtractor;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Mateffy\Magic\Support\JsonValidator;

/**
 * @property-read ExtractionBucket $bucket
 * @property-read SavedExtractor $saved_extractor
 * @property-read ?ExtractionRun $run
 */
class Generator extends Component
{
    use HasPartialJson;

    #[Locked]
    public string $extractorId;

    #[Locked]
    public ?string $bucketId = null;

    #[Locked]
    public ?string $runId = null;

    #[Reactive]
    public bool $debugModeEnabled = false;

    public ?string $actorTab = null;

    #[Computed]
    public function saved_extractor(): SavedExtractor
    {
        return SavedExtractor::findOrFail($this->extractorId);
    }

    #[Computed]
    public function bucket(): ?ExtractionBucket
    {
        return $this->bucketId
            ? ExtractionBucket::findOrFail($this->bucketId)
            : null;
    }

    #[Computed]
    public function run(): ?ExtractionRun
    {
        return $this->runId
            ? $this->bucket?->runs()->findOrFail($this->runId) ?? ExtractionRun::findOrFail($this->runId)
            : null;
    }

    #[Computed]
    public function schema(): ?array
    {
        return $this->saved_extractor?->json_schema ?? $this->run?->target_schema;
    }

    #[Computed]
    public function actors(): Collection
    {
        return $this->run?->actors ?? collect();
    }

    #[Computed]
    public function messages(): ?Collection
    {
        return $this->run?->actors()->find($this->actorTab)?->messages ?? null;
    }

    public function mount()
    {
        $this->refreshJson();

        //        $sourceUrl = route('api.bucket.runs.generate', [
        //            'bucket' => $this->bucketId,
        //            'run' => $this->runId,
        //        ]);
        //
        //        $this->js("\$dispatch('start-streaming', { sourceUrl: '$sourceUrl' })");
    }

    public function updated(string $statePath)
    {
        if (Str::startsWith($statePath, 'resultData') && $this->run->status === ExtractionRun\RunStatus::Completed) {
            // validateData returns false if it has been already called in the last second to prevent spamming updates
            if (!$this->validateData()) {
                return;
            }

            if (count($this->validationErrors) === 0) {
                $this->run->update([
                    'result_json' => $this->resultData,
                    'partial_result_json' => $this->resultData,
                ]);

                Notification::make()
                    ->success()
                    ->title(__('Data saved'))
                    ->send();
            } else {
                do {
                    $lastError = Arr::last($lastError ?? $this->validationErrors);
                } while (is_array($lastError));

                Notification::make()
                    ->danger()
                    ->title(__('Data validation error'))
                    ->body($lastError)
                    ->send();
            }
        }
    }

    public function render()
    {
        return view('livewire.generator');
    }
}
