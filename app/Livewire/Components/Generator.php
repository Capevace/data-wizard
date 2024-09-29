<?php

namespace App\Livewire\Components;

use App\Livewire\Components\Shared\HasPartialJson;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\SavedExtractor;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Livewire\Component;

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
    public string $bucketId;

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
    public function bucket(): ExtractionBucket
    {
        return ExtractionBucket::findOrFail($this->bucketId);
    }

    #[Computed]
    public function run(): ?ExtractionRun
    {
        return $this->runId
            ? $this->bucket->runs()->findOrFail($this->runId)
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

    public function render()
    {
        return view('livewire.generator');
    }
}
